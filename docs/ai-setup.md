# AI agent setup

The agent instructions are authored in `.ai` and compiled into `CLAUDE.md` by Laravel Boost (`php artisan boost:update`, also run after every `composer update`). Never edit `CLAUDE.md` directly.

## Layout

- `.ai/guidelines/*.md`: always-loaded guidelines (orchestration, architecture, code style, dependencies, environment, translations, ...).
- `.ai/rules/*.md`: path-scoped rules. The `paths` frontmatter decides when a rule applies (eg: `.ai/rules/models.md` applies to `app/Models/**`). Requires Boost `^2.10`.
- `.ai/skills/*/SKILL.md`: project skills, published by Boost next to the package ones listed in `boost.json`:
  - `dev-workflow-setup` / `dev-workflow-finalize`: worktree in `.claude/worktrees`, dedicated test database, cleanup.
  - `worktree-local-server-setup` / `browser-smoke-testing`: throwaway database + `php artisan serve` per worktree, driven through Claude in Chrome.

## Tooling

- Composer `:agentic` script variants (`test`, `coverage`, `type-coverage`, `mutate`, `pint`, `rector`, `stan`) emit agent-friendly output; `composer qa` runs the full quality gate.
- Pest 5 with `--tia` (runs only tests affected by the change), `pest-plugin-agent` (`--agent` one-shot tests), mutation and type coverage. Coverage is driven by PCOV in the `web-local` image; Xdebug is installed but off unless `XDEBUG_MODE` is set.
- `laravel/pao` formats tool output for agents.
- CodeGraph indexes the repo in `.codegraph/` (built on first container boot by the entrypoint) and is exposed through the `codegraph` MCP server and a `UserPromptSubmit` hook.
- Context7 MCP authenticates with `CONTEXT7_API_KEY` from `.env`; `gh` uses the project-scoped `GH_TOKEN`.
