<?php

declare(strict_types=1);

arch('every file declares strict types')
    ->expect('Wobqqq\IdeHelper')
    ->toUseStrictTypes();

arch('no debugging calls are left behind')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'ray', 'die', 'exit'])
    ->not->toBeUsed();

arch('every relation service implements the contract')
    ->expect('Wobqqq\IdeHelper\Services\ModelRelationships')
    ->classes()
    ->toImplement(Wobqqq\IdeHelper\Services\ModelRelationships\ModelRelationshipServiceInterface::class)
    ->ignoring(Wobqqq\IdeHelper\Services\ModelRelationships\ModelRelationshipService::class);

arch('the services are final')
    ->expect('Wobqqq\IdeHelper\Services')
    ->classes()
    ->toBeFinal();
