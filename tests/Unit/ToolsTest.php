<?php

declare(strict_types=1);

use Wobqqq\IdeHelper\Dto\RelationshipModelConfigDto;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;
use Wobqqq\IdeHelper\Tests\Fixtures\Models\Post;
use Wobqqq\IdeHelper\Tools\Tools;

it('writes a class name fully qualified exactly once', function (string $class): void {
    expect(Tools::getClassFormat($class))->toBe('\Acme\Blog\Models\Post');
})->with(['Acme\Blog\Models\Post', '\Acme\Blog\Models\Post', '\\\\Acme\Blog\Models\Post']);

it('finds the related class in every shape October accepts', function (mixed $definition): void {
    expect(Tools::getModelClass($definition))->toBe('\\' . Post::class);
})->with([
    'a class name' => [Post::class],
    'an array' => [[Post::class, 'key' => 'post_id']],
    'options first' => [['key' => 'post_id', 0 => Post::class]],
]);

it('refuses a related class that does not exist', function (mixed $definition): void {
    Tools::getModelClass($definition);
})->with([['Acme\Missing'], [['key' => 'post_id']], [null]])->throws(IdeHelperException::class);

it('derives the key column the way October does', function (string $relation, string $column): void {
    expect(Tools::getKeyColumnName($relation))->toBe($column);
})->with([
    ['parent', 'parent_id'],
    ['parentCategory', 'parent_category_id'],
    ['URL', 'u_r_l_id'],
]);

it('keeps the relation settings it was given', function (): void {
    $dto = new RelationshipModelConfigDto(October\Rain\Database\Relations\HasOne::class, 'Service', true, false, true);

    expect($dto->getRelationshipType())->toBe(October\Rain\Database\Relations\HasOne::class)
        ->and($dto->getService())->toBe('Service')
        ->and($dto->isRead())->toBeTrue()
        ->and($dto->isWrite())->toBeFalse()
        ->and($dto->isNullable())->toBeTrue();
});
