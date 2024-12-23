<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreatePollInput;
use App\Entity\AbstractCampaignTier;
use App\Entity\AbstractVoteConfig;
use App\Entity\Poll;
use App\Entity\User;
use App\Repository\PollRepository;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CreatePollProcessor implements ProcessorInterface
{

    public function __construct(
        private Security $security,
        private IriConverterInterface $iriConverter,
        private PollRepository $pollRepository,
    )
    {

    }

    /**
     * @param CreatePollInput $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Poll
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Poll
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $poll = new Poll();
        $poll
            ->setCreatedBy($user)
            ->setPollName($data->getPollName())
            ->setEndsAt($data->getEndDate());

        $this->pollRepository->persist($poll);

        foreach ($data->getVoteConfig() as $iri => $configDto) {
            try {
                /** @var AbstractCampaignTier|null $entity */
                $entity = $this->iriConverter->getResourceFromIri($iri);
                if (!$entity || !$user->getId()->equals($entity->getOwner()->getId())) {
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

        $this->pollRepository->save();

        return $poll;
    }
}
