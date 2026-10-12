# Website Project

This repository is one client website built from the agency template. The client brief is imported below and applies to every task.

@docs/brief.md

## Stack
- Local Docker only: `app` (PHP 8.4-FPM + Composer), `web` (Apache 2.4), `db` (MySQL 8.4). Owned by `docker-agent`.
- Vanilla PHP by default (`src/`, PSR-4 `App\` namespace, front controller `src/public/index.php`). Laravel only if the brief asks for it.
- Plain CSS with custom properties: `src/public/css/tokens.css` (design tokens) then `src/public/css/app.css`. No Tailwind, no CSS frameworks, no Node build step.
- All fonts, images and JS are self-hosted. The CSP in `src/public/index.php` only allows `'self'`. When the brief needs a third party (analytics, maps, booking widget), add that one origin to the specific directive and record it in `docs/launch-checklist.md`. Never `'unsafe-inline'`, never `*`.

## Execution Rules
- NEVER run PHP, Composer or MySQL on the host. Use `docker compose exec app <command>` (and `docker compose exec db mysql ...`).
- Before `docker compose up` on a fresh clone, `.env` must exist with a unique `COMPOSE_PROJECT_NAME` and `WEB_PORT`. Run `/new-site` if it does not.
- Work on `feature/<name>` branches. Commit only when asked. Don't push unless asked.
- PHP must pass `docker compose exec app composer lint` before a task is reported as done.

## Agent Pipeline
Subagents do not share memory. Each one reads its inputs from files and writes its output to files. Use them by name:

| # | Subagent | Reads | Writes | When |
|---|---|---|---|---|
| 0 | `docker-agent` | `.env`, `.docker/`, `docker-compose.yml` | infrastructure | Only for infrastructure changes or a broken environment |
| 1 | `branding-ux` | `docs/brief.md`, `docs/assets/` | `docs/brand.md` | New site, rebrand |
| 2 | `copywriter` | brief, brand | `docs/copy/<page>.md` | New site, new page, rewrite |
| 3 | `graphic-designer` | brief, brand, copy | `docs/design-spec.md`, `src/public/css/tokens.css` | New site, new page, redesign |
| 4 | `dev-agent` | all of the above | `src/` | Build or change pages and features |
| 5 | `seo-agent` | brief, `src/views/`, routes | fixes in `src/`, `robots.txt`, `sitemap.xml` | After building pages, before launch |
| 6 | `qa-reviewer` | everything, plus the running site | report in `docs/qa/` | After any visible change; always before reporting "done" |

Rules:
- Run only the stages a task needs (a copy tweak is copywriter → dev-agent → qa-reviewer, not the whole pipeline).
- Don't start a stage if its input file is missing. Run the earlier stage or ask the user.
- When qa-reviewer reports blocking issues, send them to the responsible agent, then run QA again. Don't call work finished while QA says FAIL.
- Never invent client facts (reviews, figures, addresses, accreditations). Use `[NEEDS CLIENT INPUT: ...]` and collect these for the user.

## Project Files
- `docs/brief.md`: the client brief (source of truth)
- `docs/brand.md`, `docs/design-spec.md`, `docs/copy/`: agent outputs; keep them current when the site changes
- `docs/assets/`: client-supplied logos, photos, old copy
- `docs/launch-checklist.md`: go-live checklist (UK legal, SEO, performance, hosting)

## Conventions
- British English throughout (code comments and copy).
- Views: pages render inside `src/views/layouts/main.php`; SEO meta is passed as `$data['meta']`. Escape all output with `e()`.
- Forms: copy the contact form pattern (CSRF, honeypot, server-side validation, PRG).

## Commands
- `/new-site`: set up a freshly cloned project (unique env, brief interview, first pipeline run)
- Start: `docker compose up -d` · Stop: `docker compose down` · Logs: `docker compose logs -f web app`
- Composer: `docker compose exec app composer install`
- Lint / fix: `docker compose exec app composer lint` / `composer fix`
