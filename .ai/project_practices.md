# Project Practices

## Testing instructions

### Always Test

- When creating, editing or removing a feature or a piece of code, ALWAYS create or adjust the relevant test suite accordingly.  
- Use PestPHP.
- use the /pest-testing skill
 
### Before Writing Tests
 
  1. **Check database schema** - Use `database-schema` tool to understand:
     - Which columns have defaults
     - Which columns are nullable
     - Foreign key relationship names
 
  2. **Verify relationship names** - Read the model file to confirm:
     - Exact relationship method names (not assumed from column names)
     - Return types and related models
 
  3. **Test realistic states** - Don't assume:
     - Empty model = all nulls (check for defaults)
     - `user_id` foreign key = `user()` relationship (could be `author()`, `employer()`, etc.)
     - When testing form submissions that redirect back with errors, assert that old input is preserved using `assertSessionHasOldInput()`.

## Prefer Actions

When creating or refactoring some kind of behavior, consider writing it as an Action, using lorisleiva/laravel-actions.  
Filament also has Actions, in case the behavior is filament related, create a filament action.  
Sometimes, it makes sense to mix the two approached and create a "generic" action, that gets then referenced in a filament action.  
Actions NEVER suppose to have a request available. They may be used form a CLI context, for example, where the request is not available.  
Use the /laravel-action skill.

## Only use trustable dependencies

When approaching a need that may be satisfied by an external package, make the following considerations before requiring it:
- is it maintained? If it has commits in the last 2 months, issues and PRs are not stale, then YES, it's good.  
- is it tested? If it does not present a test suite in the repo (not the dist), then it might not be trustable.  
- is it used by the community? If it has 250+ github stars or 10K+ download via packagist, consider it's good.

## Pay attention to Static Analysis

- This project uses phpstan with larastan. Try to satisfy the Static Analysis needs.'
- Prefer the usage of safe functions offered by thecodingmachine/safe

## Comments

- Don't generate code comments above the methods or code blocks if they are obvious. Don't add docblock comments when defining variables, unless instructed to, like `/** @var \App\Models\User $currentUser */`. Generate comments only for something that needs extra explanation for the reasons why that code was written.
- Always write comments in english
- Add PHPDoc blocks on methods only in the following cases:
    - the method is public
    - the method must be type hinted with phptsan annotations/syntax that is more precise than built in php. Example: array shapes, generics, ...

## Casing Preferences

- variables: use snake_case

## Documentation

- always use official docs as preference. Eg: prefer an approach described in the laravel/filament/spatie/... doc instead of one found elsewhere, if they produce the same result.
- for library documentation, if some library is not available in Laravel Boost 'search-docs', always use context7. Automatically use the Context7 MCP tools to resolve library id and get library docs without me having to explicitly ask.

## PHP instructions
 
- In PHP, use `match` operator over `switch` whenever possible
- Generate Enums always in the folder `app/Enums`, not in the main `app/` folder, unless instructed differently.
- Always use Enum value as the default in the migration if column values are from the enum. Always casts this column to the enum type in the Model.
- Don't create temporary variables like `$currentUser = auth()->user()` if that variable is used only one time.
- Always use Enum where possible instead of hardcoded string values, if Enum class exists. For example, in Blade files, and in the tests when creating data if field is casted to Enum then use that Enum instead of hardcoding the value.
- In views, or where imports are not available, prefer helpers over facades. For example, use str()->slug('An Example') instead of Str::slug('An Example').
- Only where imports are available, import the facade instead of using the global namespace alias.
- **CRITICAL** always use strict types and fully type hint

## Laravel instructions
 
- Using Services in Controllers: if Service class is used only in ONE method of Controller, inject it directly into that method with type-hinting. If Service class is used in MULTIPLE methods of Controller, initialize it in Constructor.
- **Eloquent Observers** should be registered in Eloquent Models with PHP Attributes, and not in AppServiceProvider. Example: `#[ObservedBy([UserObserver::class])]` with `use Illuminate\Database\Eloquent\Attributes\ObservedBy;` on top
- Aim for "slim" Controllers and put larger logic pieces in fat models or actions
- Prefer to use Services only for external integrations
- Use Laravel helpers instead of `use` section classes. Examples: use `auth()->id()` instead of `Auth::id()` and adding `Auth` in the `use` section. Other examples: use `redirect()->route()` instead of `Redirect::route()`, or `str()->slug()` instead of `Str::slug()`.
- Always add `::query()` when running Eloquent `create()` statements. Example: instead of `User::query()->create()`, use `User::create()`.
- In Livewire projects, don't use Livewire Volt. Only Livewire class components.
- Never chain multiple migration-creating commands (e.g., `make:model -m`, `make:migration`) with `&&` or `;` — they may get identical timestamps. Run each command separately and wait for completion before running the next.
- Enums: If a PHP Enum exists for a domain concept, always use its cases (or their `->value`) instead of raw strings everywhere — routes, middleware, migrations, seeds, configs, and UI defaults.
- Controllers: Single-method Controllers should use `__invoke()`; multi-method RESTful controllers should use `Route::resource()->only([])`
- Don't create Controllers with just one method which just returns `view()`. Instead, use `Route::view()` with Blade file directly.
- Always use Laravel's @session() directive instead of @if(session()) for displaying flash messages in Blade templates.
- In Blade files always use `@selected()` and `@checked()` directives instead of `selected` and `checked` HTML attributes. Good example: @selected(old('status') === App\Enums\ProjectStatus::Pending->value). Bad example: {{ old('status') === App\Enums\ProjectStatus::Pending->value ? 'selected' : '' }}.
- use the /laravel-best-practices and /laravel-boost:laravel-code-simplifier skills when writing laravel code
 
## Filament Rules
 
- When generating Filament resource, you MUST generate Filament smoke tests to check if the Resource works. When making changes to Filament resource, you MUST run the tests (generate them if they don't exist) and make changes to resource/tests to make the tests pass.
- When generating Filament resource, don't generate View page or Infolist, unless specifically instructed.
- When referencing the Filament routes, aim to use `getUrl()` instead of Laravel `route()`. Instead of `route('filament.admin.resources.class-schedules.index')`, use `ClassScheduleResource::getUrl('index')`. Also, specify the exact Resource name, instead of `getResource()`.
- When using Enum class for Eloquent Model field, add Enum `HasLabel`, `HasColor` and `HasIcon` interfaces if aren't added yet instead of specifying values/labels/colors/icons inside of Filament Forms/Tables. **CRITICAL**: Always use the exact return type declarations from the interface definitions - do NOT substitute specific types (e.g., use `string|BackedEnum|Htmlable|null` for `getIcon()`, not `string|Heroicon|null`). When defining a default using enum never add `->value`. Refer to this docs page: https://filamentphp.com/docs/5.x/advanced/enums
- Always use Enum instead of hardcoded string value where possible, if Enum class exists. For example, in the tests, when creating data, if field is casted to Enum, then use that Enum instead of hardcoded string value.
- When adding icons, always use the Filament enum Filament\Support\Icons\Heroicon class instead of string.
- When adding actions that require authorization, use the `->authorize('ability')` method on the action instead of manually calling `Gate::authorize()` or checking `Gate::allows()`. The `authorize()` method handles both authorization enforcement and action visibility automatically.
- In Filament v5, validation rule `unique()` has `ignoreRecord: true` by default, no need to specify it.
- In Filament v5, if you create custom Blade files with Tailwind classes, you need to create a custom theme and specify the folder of those Blade files in theme.css.
- **Deprecated v3 methods - do NOT use:**
  - `->form()` on Actions/Filters → use `->schema()` instead
  - `->mutateFormDataUsing()` → use `->mutateDataUsing()` instead
  - `Placeholder::make()` → use `TextEntry::make()->state()` instead (import from `Filament\Infolists\Components\TextEntry`)
  - `->label('')` for hidden labels → use `->hiddenLabel()` instead

## HTML & CSS
- use modern semantic for HTML tags
- try not to use custom css. Prefer tailwind.
- try not to use arbitrary tailwind classes, if possible.
- use the /tailwind-css-development skill when writing Tailwind

## Translations
- always use the english phrase as translation key
- always generate the translated phrase in italian, in lang/it.json
- in blade, prefer {{ __('something') }} instead of @lang()
- use placeholders (:placeholder) in translation keys, instead of sprintf. 

## Logs
Available logs files in storage/logs:
- nginx.log
- nginx_access.log
- nginx_error.log
- php-fpm.log
- laravel.log
- cron.log
- vite-watch.log
- supervisord.log
- worker.log
