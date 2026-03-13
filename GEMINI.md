## Local AI only

This environment uses only local LLMs through Ollama.

Gemini CLI is only an orchestrator.

Available local tools:
- coder → deepseek-coder:6.7b
- qwen → qwen2.5:7b

Rules:
- For any coding, refactoring, file creation, PHP changes, HTML/CSS changes, SQL, and scripts use `coder`
- For reasoning, debugging, architecture review, Linux, deployment review, and code analysis use `qwen`
- Do not use Gemini cloud models to generate large code blocks
- Prefer local models for everything
- If code must be generated, generate it with local Ollama models only

Examples:
- `coder refactor this PHP page into smaller includes`
- `coder create reusable header/footer/template structure`
- `coder clean this contact form and validation`
- `coder create sqlite migration and repository`
- `qwen analyze this PHP codebase and suggest safer structure`
- `qwen review deployment risks`

## Mandatory rules

### General
- Work only in this repository
- Keep changes incremental
- Do not break working pages without reason
- Avoid unnecessary dependencies
- Do not introduce frameworks unless explicitly requested

### PHP structure
Prefer this direction:

- `public/` for entry files if restructuring is needed
- `inc/` for shared bootstrap/config
- `templates/` for partials
- `src/` for reusable logic if the project grows
- small helper files instead of giant mixed pages

### Code style
- Keep files reasonably small
- Avoid large monolithic PHP files when possible
- Separate HTML, business logic, and data access
- Prefer reusable functions and includes
- Prefer prepared statements for database work
- Avoid hardcoded secrets
- Move secrets to local non-versioned config files

### Configuration and secrets
- Never expose real passwords, API keys, SMTP passwords, or production secrets
- Use example config files for repository-safe templates
- Keep real config only in local files outside git tracking
- Prefer:
  - `inc/connect.php` for real local config
  - `inc/connect.example.php` for safe example config

### Database
- MySQL
- Keep schema simple
- Do not add database complexity without a clear reason

## Git workflow

Work in git safely.

Rules:
- Never commit directly to main/master unless explicitly told
- Use the current working branch unless instructed otherwise
- Make small atomic commits
- Before push, show a short summary of changed files
- Do not force-push unless explicitly requested
- Do not change remotes unless explicitly requested

Allowed workflow:
1. inspect current files
2. propose structure
3. refactor incrementally
4. verify syntax
5. summarize changes
6. commit if requested
7. push if requested

## Refactoring priorities

When improving this project, prefer this order:
1. remove secrets from tracked files
2. stabilize includes/config
3. reduce duplication
4. split shared layout parts
5. improve form handling
6. improve maintainability
7. improve visual consistency
8. add small features safely

## UI expectations

For frontend work:
- keep the site clean and simple
- improve structure without overengineering
- preserve content unless asked to rewrite it
- prefer reusable layout parts
- keep CSS manageable
- avoid huge inline style blocks where possible

## What to avoid

- huge rewrites without checkpoints
- introducing unnecessary frameworks
- generating giant single-file applications
- hardcoding secrets
- breaking includes or relative paths
- changing deployment assumptions without reason
- using cloud models instead of local Ollama models

## Autonomous mode

Work autonomously.
Do not ask unnecessary questions.
If something is missing:
- make conservative assumptions
- continue with the safest reasonable implementation
- keep changes reversible
- prefer minimal-risk progress

