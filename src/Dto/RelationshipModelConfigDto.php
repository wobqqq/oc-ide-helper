<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Dto;

final class RelationshipModelConfigDto
{
    /** @var string */
    private $relationshipType;

    /** @var string */
    private $service;

    /** @var bool */
    private $isRead;

    /** @var bool */
    private $isWrite;

    /** @var bool */
    private $isNullable;

    public function __construct(string $relationshipType, string $service, bool $read, bool $write, bool $nullable)
    {
        $this->relationshipType = $relationshipType;
        $this->service = $service;
        $this->isRead = $read;
        $this->isWrite = $write;
        $this->isNullable = $nullable;
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
