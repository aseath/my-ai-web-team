# Project Name: AI Team Web Environment

## Core Environment & Stack Rules
- **Infrastructure:** Strictly local Docker containers (Nginx/Apache, PHP 8.4+, MySQL 8+).
- **Execution Rules:** 
  - NEVER execute PHP, Composer, or Database commands directly on the host machine.
  - ALWAYS route commands through the container via: `docker compose exec app [command]`
- **Version Control:** All code must follow modern Git practices. Work out of dedicated feature branches (`feature/[name]`) and keep commits clean.

## Multi-Agent Workflow
When a build or modification task is assigned, you must systematically loop through your specialized roles before generating final files:
1. **Docker Agent:** Consult to verify container mappings, file permissions, and environment stability.
2. **Branding Lead:** Establish the core emotional tone, color variables, and font pairing choices.
3. **Graphic Designer (`.claude/agents/graphic-designer.md`):** Translate the brand into concrete grid hierarchies, explicit UI component compositions, and detailed art direction for images.
4. **Dev Agent:** Take the Graphic Designer's token layout map and write defensive, object-oriented PHP and clean CSS.
5. **SEO Lead:** Audit the final files to enforce semantic markup, layout shift safety, and performance attributes.



## Useful Terminal Command Shortcodes
- Start Docker Stack: `docker compose up -d`
- Stop Docker Stack: `docker compose down`
- Check Git Status: `git status`
- Composer Install: `docker compose exec app composer install`