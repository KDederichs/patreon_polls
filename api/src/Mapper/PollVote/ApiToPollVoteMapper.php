<?php

namespace App\Mapper\PollVote;

use App\ApiResource\PollVoteApi;
use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Entity\User;
use App\Mapper\AbstractApiToObjectMapper;
use App\Repository\PollVoteRepository;
use App\Service\VoteConfigService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PollVoteApi::class, to: PollVote::class)]
final class ApiToPollVoteMapper extends AbstractApiToObjectMapper
{

    public function __construct(
        private readonly Security $security,
        private readonly MicroMapperInterface $microMapper,
        private readonly PollVoteRepository $voteRepository,
        private readonly VoteConfigService $configService
    )
    {

    }

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof PollVoteApi);

        return $dto->getId() ? $this->voteRepository->find($dto->getId()) : new PollVote();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof PollVoteApi);
        assert($entity instanceof PollVote);

        $user = $this->security->getUser();
        assert($user instanceof User);
        if (!$entity->getVotedBy()) {
            $entity->setVotedBy($user);
        }

        /** @var PollOption $pollOption */
        $pollOption = $this->microMapper->map($dto->getPollOption(), PollOption::class, [
            'mode' => AbstractApiToObjectMapper::POPULATION_MODE_PASSTHROUGH
        ]);

        $config = $this->configService->getConfigForUser($pollOption->getPoll(), $user);

        return
            $entity
                ->setPoll($pollOption->getPoll())
                ->setOption($pollOption)
                ->setVotePower($config?->getVotingPower() ?? 1);
    }
}
