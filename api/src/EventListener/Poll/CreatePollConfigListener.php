<?php

namespace App\EventListener\Poll;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\PollApi;
use App\Entity\AbstractCampaignTier;
use App\Entity\AbstractVoteConfig;
use App\Entity\Poll;
use App\Event\StateProcessor\StatePrePersistEvent;
use App\Repository\PollRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: StatePrePersistEvent::class, method: 'onPrePersist')]
final readonly class CreatePollConfigListener
{

    public function __construct(
        private IriConverterInterface $iriConverter,
        private PollRepository $pollRepository,
    )
    {

    }

    public function onPrePersist(StatePrePersistEvent $event)
    {
        if ($event->getEntityClass() !== Poll::class) {
            return;
        }

        /** @var Poll $poll */
        $poll = $event->getEntity();
        /** @var PollApi $dto */
        $dto = $event->getDto();

        foreach ($dto->getVoteConfig() as $iri => $configDto) {
            try {
                /** @var AbstractCampaignTier|null $entity */
                $entity = $this->iriConverter->getResourceFromIri($iri);
                if (!$entity || !$poll->getCreatedBy()->getId()->equals($entity->getOwner()->getId())) {
                    continue;
                }

                $voteConfigClass = $entity->getVoteConfigClass();
                /** @var AbstractVoteConfig $voteConfig */
                $voteConfig = new $voteConfigClass();
                $voteConfig
                    ->setCampaignTier($entity)
                    ->setPoll($poll)
                    ->setAddOptions($configDto->getCanAddOptions())
                    ->setLimitedVotes($configDto->getHasLimitedVotes())
                    ->setMaxOptionAdd($configDto->getNumberOfOptions())
                    ->setNumberOfVotes($configDto->getNumberOfVotes())
                    ->setVotingPower($configDto->getVotingPower());
                $this->pollRepository->persist($voteConfig);
            } catch (\Exception) {}
        }
    }
}
