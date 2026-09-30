<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Dto;

final readonly class RelationshipModelConfigDto
{
    public function __construct(private string $relationshipType, private string $service, private bool $isRead, private bool $isWrite, private bool $isNullable)
    {
    }

    public function getRelationshipType(): string
    {
        return $this->relationshipType;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function isWrite(): bool
    {
        return $this->isWrite;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }
}
