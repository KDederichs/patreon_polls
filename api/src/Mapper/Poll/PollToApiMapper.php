<?php

namespace App\Mapper\Poll;

use App\ApiResource\PollApi;
use App\Entity\Poll;
use App\Mapper\AbstractObjectToApiMapper;
use Symfonycasts\MicroMapper\AsMapper;

#[AsMapper(from: Poll::class, to: PollApi::class)]
final class PollToApiMapper extends AbstractObjectToApiMapper
{

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Poll);

        return new PollApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Poll);
        assert($dto instanceof PollApi);

        return $dto
            ->setId($entity->getId())
            ->setCreatedAt($entity->getCreatedAt())
            ->setEndsAt($entity->getEndsAt())
            ->setAllowPictures($entity->isAllowPictures())
            ->setPollName($entity->getPollName())
        ;
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        // TODO: Implement populateExternal() method.
    }
}
