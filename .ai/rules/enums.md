---
paths:
    - app/Enums/**
---

# Enums

- Enum class name MUST be plural
- Use `UPPERCASE_SNAKE_CASE` for enum case names.
- An enum that backs model fields and is shown to users exposes `getLabel(): string`, returning a translated label.
- Narrow return types to the single concrete type returned (`string`, not `string|null|Htmlable`).
- Shared behaviour goes in a trait under `app/Enums/Concerns`, tested against a fixture enum in `tests/Fixtures/Enums`.
