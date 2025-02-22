<?php

declare(strict_types=1);

namespace App\Mapper;

use Symfonycasts\MicroMapper\MapperInterface;

abstract class AbstractApiToObjectMapper implements MapperInterface
{
    public const string POPULATION_MODE_PASSTHROUGH = 'passthrough';

    public function load(object $from, string $toClass, array $context): object
    {
        return $context['entity'] ?? $this->internalLoader($from, $toClass, $context);
    }

    public function populate(object $from, object $to, array $context): object
    {
        if ($context['mode'] ?? null === self::POPULATION_MODE_PASSTHROUGH) {
            return $to;
        }

        return $this->internalPopulate($from, $to, $context);
    }

    abstract protected function internalLoader(object $from, string $toClass, array $context): object;

    abstract protected function internalPopulate(object $from, object $to, array $context): object;
}
