<?php

namespace App\Mapper;

use Symfonycasts\MicroMapper\MapperInterface;

abstract class AbstractObjectToApiMapper implements MapperInterface
{
    public const string MAPPING_MODE_INTERNAL = 'internal';

    public function load(object $from, string $toClass, array $context): object
    {
        return $this->internalLoader($from, $toClass, $context);
    }

    public function populate(object $from, object $to, array $context): object
    {
        if ($context['mappingMode'] ?? null === self::MAPPING_MODE_INTERNAL) {
            return $this->internalPopulate($from, $to, $context);
        }

        $this->populateExternal($from, $to, $context);
        return $this->internalPopulate($from, $to, $context);
    }

    abstract protected function internalLoader(object $from, string $toClass, array $context): object;

    abstract protected function internalPopulate(object $from, object $to, array $context): object;

    abstract protected function populateExternal(object $from, object $to, array $context): void;
}
