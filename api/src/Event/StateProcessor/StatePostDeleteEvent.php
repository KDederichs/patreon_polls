<?php

declare(strict_types=1);

namespace App\Event\StateProcessor;

use Symfony\Contracts\EventDispatcher\Event;

class StatePostDeleteEvent extends Event
{
    public function __construct(
        private readonly string $entityClass,
    ) {
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
