# Reference

This section is generated from the public PHP API in `src/`. PHPDoc summaries, real method signatures, promoted constructor properties, enum cases and default values are included in the generated pages.

Use the practical guides for task-oriented examples, and use Reference when you need the exact object shape or method contract.

## Categories

- [Clients](generated/clients/index.md) — the two public client facades.
- [Configuration](generated/configuration/index.md) — locale, credentials, device and protocol configuration objects.
- [Services](generated/services/index.md) — the objects returned by `BydClient` and their public methods.
- [Request DTOs](generated/requests/index.md) — immutable request objects and constructor parameters.
- [Response DTOs](generated/responses/index.md) — immutable response objects and public fields.
- [Enums](generated/enums/index.md) — backed values and helper methods.
- [Exceptions](generated/exceptions/index.md) — error types and their metadata.
- [Value objects](generated/values/index.md) — strongly typed values such as `Vin`.

The generated pages are refreshed with `php tools/generate-reference.php`. CI fails if the committed reference is out of date.
