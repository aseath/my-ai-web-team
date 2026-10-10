# Project Name: AI Team Web Environment

## Core Environment & Stack Rules
- **Infrastructure:** Strictly local Docker containers (Nginx/Apache, PHP 8.4+, MySQL 8+).
- **Execution Rules:** 
  - NEVER execute PHP, Composer, or Database commands directly on the host machine.
  - ALWAYS route commands through the container via: `docker compose exec app [command]`
- **Version Control:** All code must follow modern Git practices. Work out of dedicated feature branches (`feature/[name]`) and keep commits clean.

## Multi-Agent Workflow
When a build or modification task is assigned, you must systematically loop through your specialized roles before generating final files:
1. **Docker Agent (`.claude/agents/docker-agent.md`):** Consult to verify container mappings and isolation.
2. **Branding & UX Lead (`.claude/agents/branding-ux.md`):** Consult to map accessible layouts, grid spacing, and elegant color systems.
3. **Dev Agent (`.claude/agents/dev-agent.md`):** Consult to write defensive, strictly-typed PHP backend pipelines or Laravel components.
4. **SEO Lead (`.claude/agents/seo-agent.md`):** Audit the generated files to enforce structural heading hierarchies, performance attributes, and metadata blocks.


## Useful Terminal Command Shortcodes
- Start Docker Stack: `docker compose up -d`
- Stop Docker Stack: `docker compose down`
- Check Git Status: `git status`
- Composer Install: `docker compose exec app composer install`