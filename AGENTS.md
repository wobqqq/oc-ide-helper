# AGENTS.md

Guidance for AI coding agents (Claude Code, Codex, Junie, Cursor) working in this repository.

## What this is

**oc-ide-helper** (`wobqqq/oc-ide-helper` on Packagist) is a development dependency for October CMS 3.x/4.x projects. It extends [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) (`^3.5.5`, Laravel 11 to 13, PHP 8.2+) so that `php artisan ide-helper:models` writes docblocks that describe October models correctly and pass PHPStan at level max in the projects that use them:

- October declares relations as properties (`$hasMany`, `$belongsTo`, `$attachOne`, ...), which laravel-ide-helper cannot see; the package annotates each one with its property type and its relation method;
- `$jsonable` attributes are typed as arrays;
- October's builder is not generic, so `Builder<static>` is written as `Builder`;
- a generated `@method` that shadows a real method of the model (the `get()`/`set()` of a `SettingModel`) is dropped.

Projects depend on the exact docblocks it writes: a change to the output is a change to every project's PHPStan result.

## The self-check gate (run before every commit)

Everything runs in Docker; the host needs no PHP.

```bash
make install        # composer install inside the php container
make code.fix       # composer normalize, rector, php-cs-fixer
make code.check     # validate, normalize --dry-run, audit, php -l, yaml-lint, cs, rector, PHPStan max
make test           # Pest
make test.coverage  # Pest with pcov, fails below 90 %
make ready          # all of the above
```

`make ready` must pass. PHPStan runs at `level: max` with strict rules over `src`, `config` and `tests` and **no baseline**. Advisories reported by `composer audit` are fixed by updating the package, never ignored.

## How the code is laid out

| Path | Holds |
|------|-------|
| `src/IdeHelperServiceProvider.php` | Extends laravel-ide-helper's provider: the post-migrate hook, the generator templates (they ship with laravel-ide-helper) and the publishing of `config/ide-helper.php`. Auto-discovered through `extra.laravel.providers`. |
| `config/ide-helper.php` | laravel-ide-helper's configuration with October's values: `model_locations` (`plugins/*/*/models`), `model_hooks` (`ModelHook`), October's builders in `extra`, October's helpers. Keep it in step with laravel-ide-helper's own file when that one gains a key. |
| `src/Hooks/ModelHook.php` | The `ModelHookInterface` laravel-ide-helper calls for every model; runs the services below, the October ones only for `October\Rain\Database\Model`. |
| `src/Services/ModelRelationships/` | One service per relation shape (single model, collection, belongsTo, morphTo); `ModelRelationshipService::RELATIONSHIPS` maps every October relation property to one. A relation that cannot be resolved is skipped with a warning, never fatal. |
| `src/Services/ModelJsonableService.php`, `ModelBuilderGenericService.php`, `ModelInheritedMethodService.php` | The jsonable, builder and shadowed-method corrections. |
| `src/Tools/Tools.php` | Class-name formatting, the related class of a definition, key column names, nullability; the only code that reaches into `ModelsCommand` through reflection. |
| `tests/Fixtures/Models/` | October models covering every relation type, jsonable columns, a settings model and a plain Eloquent model; `tests/TestCase.php` creates their tables. |

## Compatibility rules

- **Semver on the output.** A change that makes regenerated docblocks stricter or different (a type that loses `|null`, a new `@property`) is a minor version with a note in the release; fixing a docblock that did not match what October returns is a fix. Never change the output silently.
- **laravel-ide-helper's internals move.** The reflection in `Tools` and the services reads `ModelsCommand::$methods`, `$nullableColumns` and `getClassNameInDestinationFile()`. Before raising the constraint, run the suite on the new version and on the lowest one (`composer update --prefer-lowest`; CI does both).
- **October versions.** The package must work with the `october/rain` of October 3.x and 4.x. Read relation definitions the way `October\Rain\Database\Concerns\HasRelationships` does (the related class is the definition or its first positional element; `key`, `name`, `id` options).
- Keep the public classes and their constructor signatures: projects register `ModelHook` in their own published config.

## Security

It is a development tool, but it runs inside the project: it never writes outside the files laravel-ide-helper is asked to write, never executes model code beyond instantiating the model, and never reads `.env`, `auth.json` or other secrets. A dependency advisory is fixed like in production code.

## Conventions

- `declare(strict_types=1);` in every PHP file; PSR-12 via php-cs-fixer (`(int)$x` without a space, imported classes).
- Code documents itself; a docblock explains a non-obvious *why* (every service starts with the October behaviour it corrects).
- Services are `final`; the DTO is `final readonly`.
- Every change to the generated output is pinned by a test in `tests/Feature/ModelsCommandTest.php` that runs the real `ide-helper:models`.
- Commits: imperative subject saying what changes in the generated docblocks, a body with the why.
