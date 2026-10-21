<laravel-boost-guidelines>
=== .ai/agents_orchestration rules ===

# Agents Orchestration

Set of rules, best practice and guidelines an AI agent must follow when working on any task:

## Session and guardrails

- Always use a worktree, not only a branch, even for a documentation-only task. When work needs to happen directly on main, the user will specify that. Use the `/dev-workflow-setup` skill to start and the `/dev-workflow-finalize` skill to close out.
- Only perform one task/issue per session. When a task is finalized, recommend the user start the next task in a fresh session (/clear) instead of continuing here.
- Create a new base folder only with the user's approval.
- Never disable PAO.
- NEVER edit CLAUDE.md: it is a file generated from laravel boost.
- NEVER edit rules, guidelines, skill definitions, nor any file inside `.ai` or `.claude`. The only exception is via EXPLICIT authorization given by the user. If instructed to touch those file, then run `php artisan boost:update`.
- Never commit without the user's explicit approval. Split the work in atomic commits grouped by logical and domain changes that belong together.

## Gathering context

- Never `cat`/`tail -N` a whole file into context.
- Prefer `Explore` over `grep` when a wide sweep is needed and/or a known symbol/specific signature is not provided. Prefer `Grep`/`Read` otherwise.
- An approved plan states the file paths, conventions, helper names, and test seams resolved during planning. Read them from there, do not re-explore or re-verify.
- To get packages documentation, use Boost's `search-docs` first. If a package is not covered there, consult `context7 mcp`.
- Prefer official documentation over any other source when both produce the same result (Laravel, Filament, Livewire, Pest, ...).
- This project has domain-specific skills available in **/skills/**. You MUST activate the relevant skill whenever you work in that domain.
- Reach for CodeGraph BEFORE grep/find or reading files when you need to understand or locate code
  — this repo is indexed by it (a `.codegraph/` directory exists at the repo root):
  - **MCP tool** (when available): `codegraph_explore` answers most code questions in one call — the
    relevant symbols' verbatim source plus the call paths between them, including dynamic-dispatch
    hops grep can't follow. Name a file or symbol in the query to read its current line-numbered
    source. If it's listed but deferred, load it by name via tool search.
  - **Shell** (always works): `codegraph explore "<symbol names or question>"` prints the same
    output.

## Delegating to subagents

- Parallelize any task as much as possible, using dedicated subagents.
  Those subagents report back only what is strictly necessary and should avoid context pollution of the dispatching agent as much as possible.
  Subagent may dispatch their own subagents.
  The dispatching agent keeps decisions and verdicts, the bulk output stays in the subagent.
- Do not reuse the same subagent for a new task.
- Use less token-expensive models, such as `haiku` for exploration.
- Use more capable models, such as `opus` for planning.
- Use average capable models, such as `sonnet` for implementing a plan.

## Running commands

- Always use the :agentic script variants when available. Eg: `composer test:agentic` instead of `composer test`.
- Composer script extra flags go after `--`. Eg: `composer test:agentic -- --filtered --bail`. Composer forwards everything after `--` to the script's last command.
- Delegate formatting to `composer pint:agentic`.
- Delegate code form to `composer rector:agentic`.
- Delegate static analysis form to `composer stan:agentic`.
- Never run `composer qa` mid-task, only at the end, only if instructed.
- Generate scaffolding files with `php artisan make:*`. Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior. Use `php artisan make:test --pest {name}` to create tests with Pest.

## Testing

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Creating, editing, or removing any code means that the relevant test suite MUST be created or adjusted.
- If no code changed since the last test run, do not re-run. Nothing can have changed.
- When verification of a single feature/fix/refactor or edit is needed, run the minimum amount of tests possible. Maybe the test file, or maybe a single test, never the full suite.
- To run only tests affected by a change use `composer test:agentic -- --filtered`.
- If a one-shot, non persisted test is needed (eg: for debugging), use Pest's `--agent` flag. Use the `/pest-plugin-agent`. Eg: `--agent='$user = \App\Models\User::factory()->create(); $this->actingAs($user)->get("/admin")->assertOk();'`. Note the single quotes: they avoid shell interpolation on `$user`.

## Talking to the user

- Never use these terms without immediately explaining them with a concrete example: fold, rollup, axis (and similar data-modeling jargon).
Pattern to follow: "<term>: <one plain-language sentence>. For example, <concrete instance from the user's own data/domain>."
Bad: "Add a rollup on the sales axis."
Good: "A rollup means combining rows automatically — for example, summing all page views by month instead of showing each one. I'd add this on your 'visited_at' column."
Prioritize the example over brevity: a slightly longer message the user understands beats a short one they don't.
- Talk to the user in Italian, using translated ASD-STE100 Simplified Technical English.
- Code, comments, commit messages, skills, documentation and everything that is not direct and volatile communication with the user MUST be written in English.

=== .ai/architecture rules ===

# Architecture

- Sequential explicit logic invocation is preferred over event-driven design.
- Actions pattern wins over Observers.
- When creating or refactoring a behavior, prefer an **Action** (`lorisleiva/laravel-actions`) over a Support class, an helper method or similar.
- Filament-specific behavior must be wrapped in a Filament Action. Whenever, write a generic Action and reference it from a Filament Action. Treat Filament action as adapter/wrapper over generic actions.
- Prefer macros instead of Support classes or helpers. Eg: prefer creating macros on the `Cache` facade instead of creating a `Support\CacheHelper` class.
- One Service Provider per macro family, registering every macro on that same macroable class: all `Config` macros in `ConfigMacroServiceProvider`, all `Str` macros in `StrMacroServiceProvider`, etc.
- Use Services **only for external integrations**.
- If a Service is used in **one** controller method, inject it into that method via type-hint.
  If used in **multiple** methods, resolve it in the constructor.
- New classes are **`final` by default**.
- **Always use an enum (its case or `->value`) instead of a raw string wherever the enum exists**:
  routes, middleware, migrations, seeds, configs, UI defaults, Blade, and test data.
- Do **not** group classes by domain. Actions go in `app/Actions`, not `app/Actions/Analytics`. Same for Support, Service, etc. Exception may be allowed by explicit declaration of the user.
- Enforce architectural testing via Pest's `arch` testing, in `tests/ArchTest.php`.

=== .ai/best_practices rules ===

# Best Practices

- Stick to community best practices. Consult documentation and/or perform a web search when in need to find opinionated solutions.
- Consistency wins over everything. If the project has an established pattern, follow it for future implementations and refactors.
- Any db-relevant operation performed on a model class (eg: `Model::create()`, `Model::delete()`, `Model::update()`, ...) must run through the query builder `query` method.  
  Eg: The following is WRONG `Model::create(...)`, while the following is CORRECT `Model::query()->create(...)`.
- Prefer immutability whenever possible.
- When creating a new model or controller, generate a factory too.
- When generating links to other pages, prefer named routes and the `route()` function.
- Reference Filament routes via `Resource::getUrl('index')`, not Laravel `route()`. Name the exact Resource, not `getResource()`.
- Always use Filament enums (eg: `Filament\Support\Icons\Heroicon`, `Filament\Support\Enums\Operation`, ...) instead of raw strings.
- After creating, editing or removing a feature/fix, document it in the appropriate `.md` file in the `docs` directory.
- Update existing docs when editing/refactoring a feature. Do not append at the end or add a "modified in date" section. Edit the doc file to keep it natural, organized and fluently readable, by editing its content.
- Use conventional commits (https://www.conventionalcommits.org/en/v1.0.0/) to craft commit messages. This is a MUST.

=== .ai/code_style rules ===

# Code Style

- Always declare `strict_types=1` and fully type hint everything, constants included.
- Use **`snake_case`** for variables. Eg: the following is WRONG `$customerData`, while the following is CORRECT `$customer_data`.  
  Exceptions are possible when otherwise the code or the static analysis would break.
- Use descriptive names for variables and methods. Eg: `isRegisteredForDiscounts()`, not `discount()`.
- Start a method name with a verb that names the full action, so the call site alone tells a command from a query, without opening the definition. Eg: `exportData()` is ambiguous — it may run the export or return the rows to be exported; `getExportData()` reads as a query, `exportDataToCsv()` as a command.
- Boolean variables or methods that return bool must always start with `is` or `has`.
- Use PHP attributes, and prefer attribute-based syntax, whenever possible.
  Eg: `#[\Illuminate\Database\Eloquent\Attributes\Scope]` on a `whereActive()` method instead of `scopeWhereActive()`.
- Prefer Facades over inline helpers when method chaining is not necessary. Eg `Auth::id()` wins over `auth()->id()`.  
  Prefer helpers when method chaining degrades readability. Eg: `str('Some String')->lower()->replace('_', ' ')->ucfirst()->toString()` wins over `Str::ucfirst(Str::replace('_', ' ', Str::lower('Some String')))`.  
  If `use` statements are NOT available (eg in blade views), thus forcing Facades to be used from FQN, prefer helpers.
- Prefer the safe functions offered by `thecodingmachine/safe` over the native ones that return `false` on failure.
- Prefix compiler-optimized special functions with a backslash (eg: `\is_string()`, `\count()`, `\in_array()`). See: https://php.watch/articles/php-zend-engine-special-inlined-functions#func-list
- Use `match` over `switch` whenever possible.
- Do not create temporary variables that are used only once.
- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- NO comments. Most lines, functions, and even unusual-looking logic should ship with zero comments, trust the code and naming to speak for themselves.
Add a comment only when omitting it would likely cause a future reader to "fix" correct behavior into a bug.
Hard cap: 1-3 lines per comment. No section headers, no bullet lists, no multi-paragraph comments.
Never include: references to external docs/ADRs/tickets/issues, restatement of what the code visibly does, historical/debug/design rationale ("we chose X over Y because..."), UI labels or naming trivia.
Pattern: "<what's non-obvious> — <why, in one clause>."
- No class-explanation comments above class declaration.
- Prefer PHPDoc blocks over inline comments.
- No docblocks on simple variable declarations, except type hints like `/** @var \App\Models\User $current_user */`.
- Add PHPDoc on a method only when it is `public`, or when it needs annotations more precise than
  native PHP allows (array shapes, generics, …).
- Use array shape type definitions in PHPDoc blocks.
- This project uses PHPStan with Larastan: write code that satisfies static analysis.

=== .ai/dependencies rules ===

# Dependencies

- Change the application's dependencies only with the user's approval.
- Pull external packages whenever an issue that has already been solved by a package occurs. Do so only when the implementation cost (time, difficulty, complexity, maintenance) would be higher than writing that feature from scratch. Some smaller tasks, or some deeply custom ones, may not benefit form an extra dependency.

Before requiring an external package, check all the following:

- **Maintained**: REQUIRED. Commits in the last ~2 months, issues/PRs not stale. Inspect the repo via `gh repo view <owner>/<repo> --json pushedAt,updatedAt` and `gh api repos/<owner>/<repo>/commits` for recent activity, `gh api repos/<owner>/<repo>/contents <test-dir>` to confirm a real test suite exists.
- **Tested**: REQUIRED. A test suite present in the repo source code in the source control platform (eg GitHub). Look for the `tests` folder.
- **Adopted**: REQUIRED. 250+ GitHub stars or 10k+ Packagist downloads. If not met, check if the package is Well-Reputed. Check stars via `gh repo view <owner>/<repo> --json stargazerCount`; Packagist downloads from the package page.
- **Well-Reputed**: PREFERRED, NOT REQUIRED. Realized by a known creator. Check the author's GitHub (or similar) and perform an online search.
- **Secure**: REQUIRED. Has no security vulnerabilities known.
- When pulling a package or a library (eg: composer, npm, Dockerfile, github workflow, ...), always pin a secure, maintained and updated version. Never trust `latest`. Periodically, remind the user to bump those dependencies.

=== .ai/environment rules ===

# Environment

## Dev Environment

- IDE: VSCode
- Host Machine: Docker container running custom image, see docker/Dockerfile.
- Same image of production, plus some customization made in a dedicated if block in the docker/Dockerfile.
- Runtime:  Supervisor managed processes for: nginx, php-fpm, cron, queues, vite watch build (dev only)

### Logs

All logs live in `storage/logs`:

- `nginx.log`
- `nginx_access.log`
- `nginx_error.log`
- `php-fpm.log`
- `laravel.log`
- `cron.log`
- `vite-watch.log`
- `supervisord.log`
- `worker.log`

#### Reading logs

- Read Laravel logs via Boost MCP: `read-log-entries` for recent entries, `last-error` for the most recent exception — prefer these over `cat`/`grep`-ing `laravel.log`.
- Tail live logs with `php artisan pail`.
- Browser / JS runtime errors: the `browser-logs` Boost MCP tool.

## Production Environment

- Same Dockerfile (nginx, php-fpm, cron, queues): see docker/Dockerfile
- Served by traefik
- Custom docker-compose.yml, similar to docker-compose.local.yml, without mailpit but with watchtower
- CI/CD by Github Actions, see .github/workflows/deploy.yml
- After watchtower updates the container, by pulling the image from dockerhub, it triggers the /post-update.sh script (locally located in docker/scripts//post-update.sh)
- Opcache enabled (no CLI)
- All logs in storage/logs

=== .ai/livewire rules ===

# Livewire

- Always use class component, never use Volt syntax.

=== .ai/main rules ===

## Simone Cerruti website

Personal website for Simone Cerruti's freelancing activity: presentation, portfolio, blog and contacts.
It is localized (via `mcamara/laravel-localization`), tracks its own analytics, and is managed through a Filament admin panel served at `/admin`.

=== .ai/translations rules ===

# Translations

- Always use the English phrase as the translation key.
- Always add the Italian translation of every new key to `lang/it.json`.
- Use placeholders (`:placeholder`) in translation keys instead of `sprintf`.

=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>
