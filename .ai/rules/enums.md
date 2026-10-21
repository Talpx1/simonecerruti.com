---
paths:
    - app/Enums/**
---

# Enums

- Enum class name MUST be plural
- When creating an enum that backs model fields implement Filament's `HasLabel`, `HasColor`, `HasIcon`.
  Narrow each method's return type to the single concrete type it actually returns — all covariant, valid PHP — instead of the wide interface unions (`string|BackedEnum|Htmlable|null`).
  Eg: `getLabel(): string`, `getColor(): string` (or `array` for a custom color palette), `getIcon(): Heroicon`.
- For icons use `Filament\Support\Icons\Heroicon` cases, not strings.
- Use `UPPERCASE_SNAKE_CASE` for enum case names.
