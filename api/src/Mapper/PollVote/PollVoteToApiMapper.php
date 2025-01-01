<?php

namespace App\Mapper\PollVote;

use App\ApiResource\PollApi;
use App\ApiResource\PollOptionApi;
use App\ApiResource\PollVoteApi;
use App\Entity\PollVote;
use App\Mapper\AbstractObjectToApiMapper;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(PollVote::class, to: PollVoteApi::class)]
final class PollVoteToApiMapper extends AbstractObjectToApiMapper
{

    public function __construct(
        private readonly MicroMapperInterface $microMapper
    )
    {

    }

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof PollVote);

        return new PollVoteApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof PollVote);
        assert($dto instanceof PollVoteApi);

        return $dto
            ->setId($entity->getId())
            ->setCreatedAt($entity->getCreatedAt())
            ->setVotePower($entity->getVotePower())
            ->setPoll(
                $this->microMapper->map($entity->getPoll(), PollApi::class)
            )
            ->setPollOption(
                $this->microMapper->map($entity->getOption(), PollOptionApi::class)
            );
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        // TODO: Implement populateExternal() method.
    }
}
