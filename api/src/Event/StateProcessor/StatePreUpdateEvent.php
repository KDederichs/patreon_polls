<?php

declare(strict_types=1);

namespace App\Event\StateProcessor;

use Symfony\Contracts\EventDispatcher\Event;

class StatePreUpdateEvent extends Event
{
    public function __construct(
        private readonly string $entityClass,
        private readonly mixed $entity,
        private readonly mixed $originalEntity,
        private readonly mixed $dto,
    ) {
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntity(): mixed
    {
        return $this->entity;
    }

    public function getDto(): mixed
    {
        return $this->dto;
    }

    public function getOriginalEntity(): mixed
    {
        return $this->originalEntity;
    }
}
