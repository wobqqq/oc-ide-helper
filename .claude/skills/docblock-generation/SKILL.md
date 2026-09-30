---
name: docblock-generation
description: "How oc-ide-helper turns October models into docblocks. Use before changing a relation service, the hook, Tools, the published config, supporting a new October relation type or option, raising the barryvdh/laravel-ide-helper or october/rain constraint, or when a project reports a wrong or PHPStan-failing generated docblock."
license: MIT
---

# Generating docblocks for October models

## The pipeline

1. `php artisan ide-helper:models` (laravel-ide-helper's `ModelsCommand`) loads every class in `model_locations`, reads the table's columns and the model's methods, and fills its own `$properties` and `$methods`.
2. It then calls each `model_hooks` entry: `ModelHook::run($command, $model)`.
3. `ModelHook` runs, for an `October\Rain\Database\Model`, the relation, jsonable and builder services, then, for every model, the shadowed-method service. Each writes through `ModelsCommand::setProperty()`, `setMethod()` and `unsetMethod()`, or through reflection on `$methods` when laravel-ide-helper offers no API.
4. `ModelsCommand` renders the docblocks (`--write` into the model, `--nowrite` into `_ide_helper_models.php`).

## What each relation becomes

| October property | Property type | Method type | Service |
|---|---|---|---|
| `belongsTo` | `Related` or `Related\|null` by the key column's nullability (`key`, else `snake_case(name)_id`) | `Relations\BelongsTo` | `BelongsToModelRelationshipService` |
| `morphTo` | `\October\Rain\Database\Model`, nullability by the id column (`id`, else `snake_case(name ?? relation)_id`) | `Relations\MorphTo` | `MorphToModelRelationshipService` |
| `hasOne`, `hasOneThrough`, `morphOne`, `attachOne` | `Related\|null` | the October relation class | `SingleModelRelationshipService` |
| `hasMany`, `hasManyThrough`, `belongsToMany`, `morphMany`, `morphToMany`, `morphedByMany`, `attachMany` | `\October\Rain\Database\Collection<int, Related>\|null` | the October relation class | `MultipleModelRelationshipService` |

The method type is the relation class itself, written short when the model imports it (`Tools::getClassNameInDestinationFile()`), never `Relation<static>|Model`: October's relation classes are not generic and name the related model, not the declaring one.

## Adding a relation type or an option

1. Read how `October\Rain\Database\Concerns\HasRelationships` builds it in every supported October version (3.x and 4.x).
2. Map it in `ModelRelationshipService::RELATIONSHIPS` to the service whose shape it returns; write a new service only for a new shape.
3. Add it to a fixture model in `tests/Fixtures/Models/` (and its columns to `tests/TestCase.php`), then pin the exact property and method lines in `tests/Feature/ModelsCommandTest.php`.
4. Check the output against PHPStan in a real October project before releasing.

## Upgrading laravel-ide-helper or October

- The reflection targets (`ModelsCommand::$methods`, `$nullableColumns`, `getClassNameInDestinationFile()`) are private API: after a version bump, run the suite on the lowest and the highest allowed versions (`composer update --prefer-lowest`, CI's `lowest` job).
- Compare `config/ide-helper.php` with laravel-ide-helper's own file and add the new keys with its defaults, keeping October's values for `model_locations`, `model_hooks`, `extra` and `helper_files`.
