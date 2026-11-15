# AI agent setup

The agent instructions are authored in `.ai` and compiled into `CLAUDE.md` by Laravel Boost (`composer ai:update`, which runs `php artisan boost:update` and `php artisan ai:link-hooks`; it also runs after every `composer update`). Never edit `CLAUDE.md` directly.

## Layout

- `.ai/guidelines/*.md`: always-loaded guidelines (orchestration, architecture, code style, dependencies, environment, translations, ...).
- `.ai/rules/*.md`: path-scoped rules. The `paths` frontmatter decides when a rule applies (eg: `.ai/rules/models.md` applies to `app/Models/**`). Requires Boost `^2.10`.
- `.ai/hooks/*.sh`: Claude Code hook scripts (see Hooks below).
- `.ai/skills/*/SKILL.md`: project skills, published by Boost next to the package ones listed in `boost.json`:
  - `dev-workflow-setup` / `dev-workflow-finalize`: worktree in `.claude/worktrees`, dedicated test database, cleanup.
  - `worktree-local-server-setup` / `browser-smoke-testing`: throwaway database + `php artisan serve` per worktree, driven through Claude in Chrome.

## Hooks

`.ai/hooks` is the single source of the hook scripts. `php artisan ai:link-hooks` symlinks it to `.claude/hooks` (and to `.codex/hooks` when that folder exists), and `.claude/settings.json` calls the scripts from there. `.claude/hooks` is ignored by version control: `composer ai:update` recreates it.

- `PreToolUse` on `Bash`: `guard-env-files.sh` (no reading of `.env*` files), `guard-destructive-db.sh` (blocks destructive database commands), `guard-git.sh` (blocks `--no-verify`, force pushes, pushes to `main`, `reset --hard` and similar; override with `AGENT_ALLOW_GIT_RISK=1`), `guard-test-pipe.sh` (blocks test runs whose output is piped or that run in background: the Playwright daemon would hang the pipe).
- `PreToolUse` on `Read|Edit|Write|MultiEdit`: `guard-env-files.sh`.
- `PreToolUse` on `Edit|Write|MultiEdit`: `guard-generated-files.sh` (`CLAUDE.md` and `AGENTS.md` are generated).
- `PostToolUse` on `Edit|Write|MultiEdit`: `format-php.sh` runs Pint on the PHP file just written.
- `_lib.sh` holds the shared helpers.

## Status line

`composer statusline` runs `ccstatusline` with `.claude/ccstatusline/settings.json`; `.claude/settings.json` uses it as the Claude Code `statusLine`.

## Updating the AI files

`composer ai:update` refreshes `CLAUDE.md`, guidelines, rules and skills with Boost and relinks the hooks. It is also the Composer `post-update-cmd`. Boost behaviour (excluded guidelines, rules) is configured in `config/boost.php`.

## Tooling

- Composer `:agentic` script variants (`test`, `coverage`, `type-coverage`, `mutate`, `pint`, `rector`, `stan`) emit agent-friendly output; `composer qa` runs the full quality gate.
- Pest 5 with `--tia` (runs only tests affected by the change), `pest-plugin-agent` (`--agent` one-shot tests), mutation and type coverage. Coverage is driven by PCOV in the `web-local` image; Xdebug is installed but off unless `XDEBUG_MODE` is set.
- `laravel/pao` formats tool output for agents.
- CodeGraph indexes the repo in `.codegraph/` (built on first container boot by the entrypoint) and is exposed through the `codegraph` MCP server and a `UserPromptSubmit` hook.
- Context7 MCP authenticates with `CONTEXT7_API_KEY` from `.env`; `gh` uses the project-scoped `GH_TOKEN`.
