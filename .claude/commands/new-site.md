---
description: Set up a freshly cloned website project — unique Docker env, client brief interview, then the first design/build pipeline run
argument-hint: <project-slug e.g. smith-plumbing>
---

Set up this freshly cloned template as a new client project. Project slug: `$ARGUMENTS` (if empty, ask for one: lowercase, hyphens, e.g. `smith-plumbing`).

## Step 1: Environment (do this without asking)
1. If `.env` already exists and `COMPOSE_PROJECT_NAME` is not `changeme`, skip to Step 2.
2. Copy `.env.example` to `.env` with Bash (`cp .env.example .env`). Do NOT use the Read tool on `.env`.
3. With `sed -i` set:
   - `COMPOSE_PROJECT_NAME` to the slug with hyphens replaced by underscores
   - `WEB_PORT` to a free port: start at 8081 and pick the first one not in `docker ps --format '{{.Ports}}'`
   - `DB_DATABASE` and `DB_USERNAME` to the slug (underscores)
   - `DB_PASSWORD` and `DB_ROOT_PASSWORD` to `$(openssl rand -hex 16)`
   - `APP_URL` to `http://localhost:<WEB_PORT>`
4. Create a branch `feature/initial-build`.
5. Run `docker compose up -d --build`, then `docker compose exec app composer install` (if `composer lint` then reports php-cs-fixer is missing, run `composer update` once and tell the user to commit `src/composer.lock` back to the template), then check `http://localhost:<WEB_PORT>/health` returns 200. If anything fails, hand it to the `docker-agent` subagent.

## Step 2: Brief interview
`docs/brief.md` is the source of truth for every agent. Fill it in with the user:
1. Ask whether they have existing material (a questionnaire, notes, an old site URL, files in `docs/assets/`). If so, read it first and pre-fill what you can.
2. Ask about the remaining gaps section by section with AskUserQuestion, in small batches (max 4 questions at a time). Don't ask about anything already answered.
3. If there's an existing site URL, fetch it and extract services, contact details and any reusable copy into the brief (mark these "from old site").
4. Write `docs/brief.md`. Leave unknowns as `TBC`. Never invent facts. Also fill `src/config/site.php` (name, description, legal details, nav) and set `CONTACT_EMAIL` in `.env` with `sed -i`.
5. Show the user a short summary and ask them to approve it. On approval, change the status line to `Status: Approved`.

## Step 3: First pipeline run
In order, using the subagents by name:
1. `branding-ux` → `docs/brand.md`. Show the user the positioning, palette and fonts and get a thumbs-up before continuing; this is the cheapest point to change direction.
2. `copywriter` → `docs/copy/*.md`
3. `graphic-designer` → `docs/design-spec.md` + `src/public/css/tokens.css`
4. `dev-agent` → build the pages
5. `seo-agent` → audit and fix
6. `qa-reviewer` → review; loop fixes back to the responsible agent until the verdict is PASS or only client-input items remain

## Step 4: Hand back
Give the user: local URL, pages built, the QA verdict, and one consolidated list of `[NEEDS CLIENT INPUT]` items to send to the client. Don't commit unless asked.
