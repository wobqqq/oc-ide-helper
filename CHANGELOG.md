# Changelog

All notable changes to this package are documented here. The project follows [semantic versioning](https://semver.org/).

## [2.2.1] - 2026-09-30

### Changed

- The Packagist page links to the documentation, the issues and the security policy, and the README's links work outside GitHub.

## [2.2.0] - 2026-09-30

### Fixed

- `morphOne` relations are typed as one model (`Related|null`) instead of a collection.
- `morphedByMany` relations are documented; they were left out.
- A related class written with a leading backslash (`'\Acme\Blog\Models\Post'`) is read correctly; it made the whole model fail.
- A relation to a class that does not exist is skipped with a warning; it stopped the model from being documented.
- A plain Eloquent model found in the model directories is documented without the October corrections; the hook failed on it.
- The related class of a definition is its first positional element, as October reads it, even when options come first.
- The `ide-helper:generate` and `ide-helper:meta` templates are loaded from laravel-ide-helper when its own provider is not registered.

### Changed

- The service provider is auto-discovered.
- The published configuration follows laravel-ide-helper 3.7 (`write_query_methods`, `write_model_relation_exists_properties`, `enforce_nullable_relationships`, `soft_deletes_force_nullable`, `macro_default_return_types`) and loads Laravel's foundation helpers next to October's.
- PHP 8.2 is required, as laravel-ide-helper 3.5.5 already did.

## 2.1.0 - 2026-09-09

- Relations are annotated with the October relation class they return; `belongsTo` and `morphTo` follow the nullability of their key column; a settings model's own `get()`/`set()` are no longer shadowed.

## 2.0.0 - 2026-07-08

- Generated model docblocks are PHPStan-clean at level max.

## 1.0.1 - 2025-09-11

- Dependency versions updated.

## 1.0.0 - 2024-04-05

- First release.
