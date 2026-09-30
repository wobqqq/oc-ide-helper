# IDE Helper for October CMS

[![CI](https://github.com/wobqqq/oc-ide-helper/actions/workflows/ci.yml/badge.svg)](https://github.com/wobqqq/oc-ide-helper/actions/workflows/ci.yml)
[![Latest version](https://img.shields.io/packagist/v/wobqqq/oc-ide-helper)](https://packagist.org/packages/wobqqq/oc-ide-helper)
[![Downloads](https://img.shields.io/packagist/dt/wobqqq/oc-ide-helper)](https://packagist.org/packages/wobqqq/oc-ide-helper)
[![PHP](https://img.shields.io/packagist/dependency-v/wobqqq/oc-ide-helper/php)](composer.json)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen)](phpstan.neon.dist)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE.md)

**Complete PHPDocs for October CMS models, directly from the source.**

This package extends [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) with what it cannot see in an October CMS project, so that your IDE autocompletes models correctly and the generated docblocks pass PHPStan at level max:

- **Relations declared as properties** (`$belongsTo`, `$hasMany`, `$attachOne`, `$morphTo`, ... every October relation type) become `@property-read` and relation `@method` annotations with the type they really return: a model, a model or `null` depending on the key column, or an `October\Rain\Database\Collection<int, Model>`.
- **Jsonable attributes** (`$jsonable`) are typed as arrays, nullable like their column.
- **October's query builder** is written without the `<static>` type argument it does not declare.
- **Settings models** keep their own `get()` and `set()`: the Eloquent `get()` annotation no longer hides `Settings::get('key')`.
- Models are looked for in `plugins/*/*/models` out of the box.

```php
/**
 * @property int $id
 * @property int|null $parent_id
 * @property array<array-key, mixed>|null $meta
 * @property-read \Acme\Blog\Models\Category|null $parent
 * @property-read \October\Rain\Database\Collection<int, \Acme\Blog\Models\Post>|null $posts
 * @property-read \System\Models\File|null $cover
 * @method static \October\Rain\Database\Relations\BelongsTo parent()
 * @method static \October\Rain\Database\Relations\HasMany posts()
 * @method static \October\Rain\Database\Builder|Category query()
 */
class Category extends Model
```

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Development](#development)
- [License](#license)

## Requirements

- PHP 8.2 or higher
- October CMS 3.x or 4.x (Laravel 11, 12 or 13)

## Installation

Require the package as a development dependency:

```bash
composer require --dev wobqqq/oc-ide-helper
```

The service provider is discovered automatically. Publish the configuration, which already points at October's plugins, builders and helpers:

```bash
php artisan vendor:publish --provider="Wobqqq\IdeHelper\IdeHelperServiceProvider" --tag=config
```

If your project already has a `config/ide-helper.php` from laravel-ide-helper, keep it and add the hook and October's model directory to it:

```php
'model_locations' => [
    './plugins/*/*/models/',
],

'model_hooks' => [
    Wobqqq\IdeHelper\Hooks\ModelHook::class,
],
```

## Usage

```bash
php artisan ide-helper:generate                   # PHPDocs for the facades
php artisan ide-helper:models --write --reset     # PHPDocs written into every model
php artisan ide-helper:meta                       # PhpStorm meta file
```

Every option of the commands is described in [laravel-ide-helper's documentation](https://github.com/barryvdh/laravel-ide-helper#usage). A relation whose class cannot be found is skipped with a warning, and the rest of the model is still documented.

A typical Composer script for a project:

```json
"code.ide": [
    "@php artisan ide-helper:generate",
    "@php artisan ide-helper:models --write --reset --quiet",
    "@php artisan ide-helper:meta"
]
```

## Development

The toolchain runs in Docker, the host needs nothing but `docker` and `make`:

```bash
make install        # composer install
make code.fix       # composer normalize, Rector, PHP CS Fixer
make code.check     # composer validate/audit, php -l, YAML lint, PHP CS Fixer, Rector, PHPStan (level max)
make test.coverage  # Pest with coverage (90 % minimum)
make ready          # everything above
```

The tests run the real `ide-helper:models` over October models covering every relation type (`tests/Fixtures/Models`) and check the docblocks it writes. GitHub Actions runs them on every pull request, on the latest and on the lowest supported dependencies, plus a syntax check on PHP 8.2. See [CHANGELOG.md](CHANGELOG.md) for the changes of each version and [SECURITY.md](SECURITY.md) to report a vulnerability.

## License

The IDE Helper for October CMS is open-sourced software licensed under the [MIT license](LICENSE.md).
