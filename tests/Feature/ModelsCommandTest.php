<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('types a belongsTo by the nullability of the column it reads', function (): void {
    expect(generatedDocblock('Category'))
        ->toContain('@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Category|null $parent')
        ->toContain('@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Author $owner')
        ->toContain('@method static \October\Rain\Database\Relations\BelongsTo parent()');
});

it('types each relation as a model or a collection of models', function (string $model, string $line): void {
    expect(generatedDocblock($model))->toContain($line);
})->with([
    'hasOne' => ['Author', '@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Profile|null $profile'],
    'hasOneThrough' => ['Author', '@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Comment|null $latestComment'],
    'morphOne' => ['Author', '@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Image|null $avatar'],
    'morphMany' => ['Author', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Image>|null $images'],
    'attachOne' => ['Author', '@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Image|null $photo'],
    'attachMany' => ['Author', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Image>|null $documents'],
    'hasMany' => ['Category', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Post>|null $posts'],
    'hasManyThrough' => ['Category', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Comment>|null $comments'],
    'belongsToMany' => ['Post', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Author>|null $authors'],
    'morphToMany' => ['Post', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Tag>|null $tags'],
    'morphedByMany' => ['Tag', '@property-read \October\Rain\Database\Collection<int, \Wobqqq\IdeHelper\Tests\Fixtures\Models\Post>|null $posts'],
]);

it('annotates the relation methods with the October relation class', function (string $model, string $line): void {
    expect(generatedDocblock($model))->toContain($line);
})->with([
    ['Author', '@method static \October\Rain\Database\Relations\MorphOne avatar()'],
    ['Author', '@method static \October\Rain\Database\Relations\AttachMany documents()'],
    ['Tag', '@method static \October\Rain\Database\Relations\MorphToMany posts()'],
    ['Category', '@method static \October\Rain\Database\Relations\HasManyThrough comments()'],
]);

it('reads a related class written with a leading backslash', function (): void {
    $docblock = generatedDocblock('Author');

    expect($docblock)->toContain('\Wobqqq\IdeHelper\Tests\Fixtures\Models\Profile|null $profile');
    expect($docblock)->not->toContain('\\\\Wobqqq');
});

it('types a morphTo by the id column it reads', function (): void {
    expect(generatedDocblock('Image'))->toContain('@property-read \October\Rain\Database\Model|null $imageable')
        ->and(generatedDocblock('Comment'))->toContain('@property-read \October\Rain\Database\Model $commentable');
});

it('types the jsonable attributes as arrays, nullable like their column', function (): void {
    expect(generatedDocblock('Post'))
        ->toContain('@property array<array-key, mixed>|null $meta')
        ->toContain('@property array<array-key, mixed> $options');
});

it('writes the October builder without a type argument it does not declare', function (): void {
    $docblock = generatedDocblock('Post');

    expect($docblock)->not->toContain('Builder<static>');
    expect($docblock)->toContain('@method static \October\Rain\Database\Builder|Post query()');
});

it('leaves the settings accessors a model really declares alone', function (): void {
    $settings = generatedDocblock('Settings');

    expect($settings)->not->toContain(' get($columns');
    expect($settings)->not->toContain(' set(');
    expect(generatedDocblock('Post'))->toContain('@method static \October\Rain\Database\Collection<int, static> get($columns = [\'*\'])');
});

it('skips a relation to a missing class with a warning and documents the rest', function (): void {
    $docblock = generatedDocblock('Broken');

    expect($docblock)->toContain('@property-read \Wobqqq\IdeHelper\Tests\Fixtures\Models\Profile|null $profile');
    expect($docblock)->not->toContain('$missing');
    expect(Artisan::output())->toContain('Wobqqq\IdeHelper\Tests\Fixtures\Models\Broken::$hasOne[\'missing\'] is skipped: Class \Acme\Blog\Models\Missing does not exist');
});

it('documents a plain Eloquent model without the October additions', function (): void {
    $docblock = generatedDocblock('Visit');

    expect($docblock)->toContain('@property int $author_id');
    expect($docblock)->not->toContain('$meta');
});
