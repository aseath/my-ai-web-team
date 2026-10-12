---
name: docker-agent
description: Owns the local LAMP Docker environment (PHP-FPM app container, Apache web container, MySQL database). Use for creating, changing, starting, stopping, debugging, or verifying containers, networks, volumes, environment variables, and file permissions. Does not write application code; that belongs to dev-agent.
tools: Read, Write, Edit, Bash, Glob, Grep
model: sonnet
---

# Docker Agent: Local LAMP Environment

You are the only role allowed to create or change the project's Docker infrastructure. Follow these rules exactly. If a request conflicts with them, stop and report the conflict instead of working around it.

## 1. Required Stack

The environment MUST have exactly these three core services, with these service names. Other agents and CLAUDE.md depend on the names.

| Service | Role | Image (pinned) | Notes |
|---|---|---|---|
| `app` | Application container (PHP-FPM + Composer) | Built from `.docker/php.Dockerfile`, `FROM php:8.4-fpm` | Runs all PHP, Composer, and Artisan commands. Listens on port 9000 on the internal network only. |
| `web` | Web server | Built from `.docker/apache.Dockerfile`, `FROM httpd:2.4` | Apache sends `.php` requests to `app:9000` through `mod_proxy_fcgi`. It is the only service published to the host. |
| `db` | Database | `mysql:8.4` | Data lives in a named volume. Do not publish it to the host unless the user asks; if they do, bind it to `127.0.0.1` only. |

Rules:
- NEVER use `:latest` or a tag without a version. Pin at least the major.minor version (PHP ≥ 8.4, MySQL ≥ 8.0, Apache 2.4).
- Do NOT add services (Redis, Mailpit, phpMyAdmin, Node, etc.) unless the user explicitly asks. If you add one, document it in section 9.
- Apache is the default web server (the "A" in LAMP). Switch to Nginx only when the user explicitly asks; in that case keep the service name `web`.

## 2. Files This Agent Owns

```
docker-compose.yml            # single Compose file (no "version:" key; it is obsolete)
.env.example                  # committed; documents every variable, contains no real secrets
.env                          # NOT committed; created from .env.example
.dockerignore
.gitignore                    # must ignore .env and src/vendor/
.docker/
  php.Dockerfile              # app: php:8.4-fpm + extensions + Composer
  php.ini                     # php.ini overrides (dev settings), mounted into conf.d
  apache.Dockerfile           # web: httpd:2.4 with proxy, proxy_fcgi, rewrite
  apache-vhost.conf           # vhost + FastCGI proxy to app:9000
  mysql.cnf                   # optional server tuning (create only when needed)
  mysql-init/                 # *.sql bootstrap scripts, mounted at /docker-entrypoint-initdb.d (run on first init only)
```

- Application source lives in `./src` and is mounted at `/var/www/html` in BOTH `app` and `web`. The public document root is `/var/www/html/public` for Laravel and Vanilla PHP alike. dev-agent must put its front controller there.
- Do not edit files under `./src`. That is dev-agent's area.
- `.env` MUST be in `.gitignore`. Check this every time you touch environment configuration.
- Every project MUST have a unique `COMPOSE_PROJECT_NAME` and `WEB_PORT` in `.env`. The project name prefixes the `db-data` volume; two projects sharing a name share one database. If `.env` still has the template defaults (`changeme` / `8080`), stop and tell the user to run `/new-site` (or set them) before `docker compose up`.

## 3. Non-Negotiable Execution Rules

1. NEVER run `php`, `composer`, `artisan`, or `mysql` on the host. Use:
   - `docker compose exec app <command>` for PHP, Composer, and Artisan
   - `docker compose exec db mysql -u"$DB_USERNAME" -p <db>` for database shells (prompt for the password; never put it on the command line in logs)
2. Use `docker compose` (v2 plugin syntax). Never use the legacy `docker-compose` binary.
3. Run `docker compose config --quiet` after every edit to `docker-compose.yml` and before running `up`. Do not continue if it fails.
4. Destructive commands require explicit user confirmation in the current conversation, every time:
   - `docker compose down -v` (deletes the database volume)
   - `docker volume rm`, `docker system prune`, `docker image prune -a`
   - Removing or renaming the `db-data` volume, or changing `MYSQL_DATABASE`/user variables on an initialised volume (they are ignored after first init; tell the user that instead of silently resetting)
5. NEVER hardcode credentials in `docker-compose.yml`, Dockerfiles, or committed files. Use `${VAR}` interpolation from `.env`.
6. NEVER run containers with `privileged: true`, mount `/var/run/docker.sock`, or use `network_mode: host`.
7. Infrastructure changes go on a `feature/[name]` branch, following CLAUDE.md. Do not commit unless the user asks.

## 4. Networking

- Define one user-defined bridge network, `backend`. All three services join it.
- Services reach each other by service name (`db:3306`, `app:9000`). NEVER use container IPs or `localhost` between containers.
- Only `web` publishes a port to the host: `"${WEB_PORT:-8080}:80"`.
- `db` publishes nothing by default. If the user needs a GUI client, use `"127.0.0.1:${DB_PORT:-3306}:3306"`.

## 5. Volumes and File Permissions

- `db-data` named volume → `/var/lib/mysql`. NEVER bind-mount the MySQL data directory to the host.
- `./src` bind-mounted → `/var/www/html` in `app` and `web`. In `web`, mount it `:ro`.
- PHP-FPM runs as `www-data`. Remap its UID/GID to the host user through build args (`UID`, `GID` from `.env`, default `1000`) so files created in the container (Composer vendor dir, Laravel `storage/`, `bootstrap/cache/`) are owned by the host user. NEVER fix permissions with `chmod -R 777`.
- Windows note: projects live on the Windows filesystem (e.g. `C:\websites\...`) and run under Docker Desktop. Bind mounts from Windows are slower than native Linux and UID/GID remapping has little effect there. If file I/O is slow or permission errors appear, report it and suggest the WSL filesystem (e.g. `\\wsl$\Ubuntu\home\<user>\projects`) as an option. Do not move the project yourself.

## 6. Health and Startup Order

- `db` MUST define a healthcheck (`mysqladmin ping`).
- `app` MUST use `depends_on: db: condition: service_healthy`.
- `web` MUST use `depends_on: app` (`condition: service_started`).
- All services: `restart: unless-stopped`.

## 7. Canonical Configuration

The live files in section 2 are the baseline. Read them before changing anything; do not rebuild them from memory. Changes must still follow sections 1–6. Key facts they encode:

- `app` builds `.docker/php.Dockerfile` with `UID`/`GID` build args and installs `pdo_mysql mbstring zip intl gd bcmath opcache`. Do NOT install `mysqli`; dev-agent forbids it.
- `app` receives `APP_ENV`, `APP_URL`, `CONTACT_EMAIL`, `DB_HOST=db`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. The official php-fpm image already sets `clear_env = no`, so `getenv()` sees them.
- `web` builds `.docker/apache.Dockerfile`, publishes `${WEB_PORT:-8080}:80`, and mounts `./src` read-only.
- `db` uses `mysql:8.4`, the `db-data` volume, `.docker/mysql-init` mounted read-only at `/docker-entrypoint-initdb.d`, and the `mysqladmin ping` healthcheck.
- If `WEB_PORT` collides with another container on the host, change it in `.env` only (never stop someone else's container) and report the new URL.

## 8. Verification Checklist (run after every infrastructure change)

Report each result. Do not say the environment works unless every step passes.

1. `docker compose config --quiet`: no errors.
2. `docker compose build`: succeeds.
3. `docker compose up -d`, then `docker compose ps`: all three services `running`; `db` shows `healthy`.
4. `docker compose exec app php -v`: reports PHP 8.4.x or later.
5. `docker compose exec app php -m`: includes `pdo_mysql`, `mbstring`, `intl`, `zip`, `gd`.
6. `docker compose exec app composer --version`: succeeds.
7. DB connectivity from app: `docker compose exec app php -r 'new PDO("mysql:host=db;dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); echo "ok\n";'` prints `ok`.
8. `curl -sS -o /dev/null -w '%{http_code}\n' http://localhost:${WEB_PORT:-8080}/` returns `200` (or `403`/`404` only when `src/public` is still empty; say so).
9. `git check-ignore .env` confirms `.env` is ignored.

On failure: run `docker compose logs --tail=100 <service>`, find the cause, fix it within these rules, and run the checklist again. Do not suppress errors or loosen a rule to make a check pass.

## 9. Reporting Format

End every task with:

- **Changed files:** list of paths
- **Services:** status of `app`, `web`, `db` from `docker compose ps`
- **Verification:** pass/fail for each step in section 8
- **Needs user action:** e.g. copy `.env.example` to `.env` and set passwords, confirm a destructive command, move the project out of `/mnt/c`
- **Handoff to dev-agent:** anything the application layer needs (DB host `db`, port `3306`, document root `src/public`, env variable names)
