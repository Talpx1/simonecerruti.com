---
paths:
    - tests/**
---

# Testing

- Use PestPHP and the `/pest-testing` skill.

## Where things live

- There is no Feature/Unit split. `tests/` mirrors the project structure: one test file per source class, at the same relative path. Eg: tests for `app/Actions/DetectVisitSource.php` live in `tests/Actions/DetectVisitSourceTest.php`.
- Sources outside `app/` mirror their root folder in StudlyCase: `resources/views/...` in `tests/Resources/Views/...`, `routes/console.php` in `tests/Routes/ConsoleTest.php`, `config/analytics.php` in `tests/Config/AnalyticsTest.php`, `public/llms.txt` in `tests/Public/LlmsTxtTest.php`.
- `tests/Browser`, `tests/ArchTest.php`, `tests/Pest.php` and `tests/TestCase.php` stay at their place.
- Large or shared datasets live in `tests/Datasets`. Fixture classes (eg: fixture enums) live in `tests/Fixtures`.
- Testing a Filament Resource: see `.ai/rules/testing_filament.md`.

## `tests/Pest.php`

- Shared setup / helpers / config live in `tests/Pest.php`, never in the test file.
- If while developing a test, a pattern is recurring, abstract a helper function in `tests/Pest.php`.
- Write custom interceptions / expectations that beautify and simplify a test in `tests/Pest.php`.

## Helper conventions

- Helper function names use `camelCase`.  
- Use the helpers Pest packages provide instead of `$this` in tests.
- Helpers should always start with a verb. Helper functions that perform expectations/assertions should have a name beginning with "assert"/"expect". Use full, unambiguous names: the reader should understand what is happening just by looking at the call, without needing to open the definition.

## Models and factories in tests

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- If while developing a test, an inline model state is recurring, abstract the state in its factory.
- Make use of `Illuminate\Database\Eloquent\Factories\Sequence`, `Model::factory()->has()`, `Model::factory()->state()`, `Model::factory()->for()`, `Model::factory()->hasAttached()` for cleaner code in tests.

## Faking and mocking

- Use the `::fake()` methods of facades in tests (`Mail::fake()`, `Notification::fake()`, etc.). Put them in the `tests/Pest.php` shared setup so they aren't forgotten.
- When using Services in a test, use `Http::fake()` instead of hitting a live API.

## Structure and rigor

- Group related tests with `describe()`. Nesting `describe` blocks is allowed.
- Test both the happy and bad scenario. Eg: a numeric value validation should be tested both by passing a numeric, thus valid, value and a non-numeric, invalid, value. Do not explicitly use "bad path"/"happy path" in the test name.
- When asserting/expecting an exception, also match its message (even partially) instead of only the exception type, to avoid false positives from another exception of the same type.
- Before/After: before verifying that an action has occurred, explicitly verify that it had not already occurred (pre-condition state).
  Eg:
  ```php
  test('marks the email as verified', function (): void {
    $user = User::factory()->unverified()->create();

    expect($user->hasVerifiedEmail())->toBeFalse() // checking the pre-condition state: email was unverified. This check ensures that the change/action actually happened and that has not always been true.
        ->and($user->markEmailAsVerified())->toBeTrue() // change/action
        ->and($user->hasVerifiedEmail())->toBeTrue() // actual test assertions
        ->and($user->fresh()?->email_verified_at)->not->toBeNull();
  });
  ```
- Before writing a test, inspect the live schema (`database-schema`) and the model: do not assume nulls on an empty model (columns may have defaults) nor relation names from foreign keys (`user_id` may back `author()`).
- When testing a form submission that redirects back with errors, also assert the old input is preserved with `assertSessionHasOldInput()`.
- Ask for approval before deleting a test.
