<?php

namespace App\EventListener\PollOption;

use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Event\StateProcessor\StatePrePersistEvent;
use App\Repository\PollVoteRepository;
use App\Service\VoteConfigService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: StatePrePersistEvent::class, method: 'onPrePersist')]
class CreateVoteListener
{
    public function __construct(
        private readonly VoteConfigService $configService,
        private readonly PollVoteRepository $voteRepository,
    )
    {

    }

    public function onPrePersist(StatePrePersistEvent $event): void
    {
        if ($event->getEntityClass() !== PollOption::class) {
            return;
        }

        $pollOption = $event->getEntity();

        assert($pollOption instanceof PollOption);

        $pollVote = new PollVote();
        $pollVote
            ->setPoll($pollOption->getPoll())
            ->setOption($pollOption)
            ->setVotedBy($pollOption->getCreatedBy())
            ->setVotePower($this->configService->getConfigForUser($pollOption->getPoll(), $pollOption->getCreatedBy())?->getVotingPower() ?? 1);

        $this->voteRepository->persist($pollVote);
    }
}
