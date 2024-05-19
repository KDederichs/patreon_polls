<?php

declare(strict_types=1);

namespace App\Event\StateProcessor;

use Symfony\Contracts\EventDispatcher\Event;

class StatePostPersistEvent extends Event
{
    public function __construct(
        private readonly string $entityClass,
        private readonly mixed $entity
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
}
