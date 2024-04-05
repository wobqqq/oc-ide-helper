# IDE Helper Generator for October CMS

**Complete PHPDocs, directly from the source**

This package generates helper files that enable your IDE to provide accurate autocompletion.
Generation is done based on the files in your project, so they are always up-to-date.
This package extends the [Laravel IDE Helper package](https://github.com/barryvdh/laravel-ide-helper)

- [Installation](#installation)
- [Usage](#usage)
- [License](#license)

## Installation

Require this package with composer using the following command:

```bash
composer require --dev wobqqq/oc-ide-helper
```

Add the following class to the `providers` array in `config/app.php`:

```php
'providers' => array_merge(include(base_path('modules/system/providers.php')), [
    Wobqqq\IdeHelper\IdeHelperServiceProvider::class,
]),
```

Publish the `config/ide-helper.php` configuration file:

```bash
php artisan vendor:publish --provider="Wobqqq\IdeHelper\IdeHelperServiceProvider" --tag=config
```

If you want, you can change the path to the models in the `config/ide-helper.php` config file:

```php
'model_locations' => [
    './plugins/MyPlugins/MyPlugin/models/'
],
```

## Usage

Basic commands:

- PHPDoc generation for Laravel Facades - `php artisan ide-helper:generate`
- PHPDocs for models - `php artisan ide-helper:models`
- PhpStorm Meta file - `php artisan ide-helper:meta`

You can get more information on usage [here](https://github.com/barryvdh/laravel-ide-helper?tab=readme-ov-file#usage).

## License

The Laravel IDE Helper Generator is open-sourced software licensed under
the [MIT license](http://opensource.org/licenses/MIT)
