<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider as BaseIdeHelperServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Wobqqq\IdeHelper\IdeHelperServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [BaseIdeHelperServiceProvider::class, IdeHelperServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make(\Illuminate\Contracts\Config\Repository::class);
        $package = require __DIR__ . '/../config/ide-helper.php';

        $config->set('ide-helper', array_merge((array)$config->get('ide-helper'), is_array($package) ? $package : []));
        $config->set('ide-helper.model_locations', [__DIR__ . '/Fixtures/Models']);
    }

    private function createSchema(): void
    {
        Schema::create('categories', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('owner_id');
        });
        Schema::create('authors', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('posts', static function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->text('meta')->nullable();
            $table->text('options');
        });
        Schema::create('tags', static function (Blueprint $table): void {
            $table->increments('id');
        });
        Schema::create('images', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('imageable_type')->nullable();
            $table->unsignedInteger('imageable_id')->nullable();
        });
        Schema::create('comments', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('subject_type');
            $table->unsignedInteger('subject_key');
        });
        Schema::create('profiles', static function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('author_id');
        });
        Schema::create('settings', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('item')->nullable();
            $table->mediumText('value')->nullable();
        });
    }
}
