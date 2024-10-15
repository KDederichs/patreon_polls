<?php

namespace App\Twig;

use App\Dto\CreatePollData;
use App\Entity\Poll;
use App\Entity\PollVoteConfig;
use App\Form\Type\CreateFormType;
use App\Repository\PatreonCampaignTierRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'PollCreator')]
class PollCreator extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public function __construct(
        private readonly PatreonCampaignTierRepository $campaignTierRepository
    )
    {

    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CreateFormType::class);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): Response
    {
        // Submit the form! If validation fails, an exception is thrown
        // and the component is automatically re-rendered with the errors
        $this->submitForm();

        $tierConfigMap = [];

        /** @var CreatePollData $data */
        $data = $this->getForm()->getData();
        $poll = new Poll();
        $poll
            ->setCampaign($data->getPatreonCampaign())
            ->setPollName($data->getPollName())
            ->setEndsAt($data->getEndDate() ? CarbonImmutable::instance($data->getEndDate()) : null);

        $this->campaignTierRepository->persist($poll);

        foreach ($data->getVoteLimit() as $tierId => $value) {
            $tierConfigMap[$tierId]['voteLimit'] = $value;
        }

        foreach ($data->getVotingPower() as $tierId => $value) {
            $tierConfigMap[$tierId]['votePower'] = $value;
        }

        foreach ($data->getMaxOptionAdd() as $tierId => $value) {
            $tierConfigMap[$tierId]['addMaxOption'] = $value;
        }

        foreach ($tierConfigMap as $tierId => $config) {
            $tierEntity = $this->campaignTierRepository->find(Uuid::fromString($tierId));
            $voteConfig = new PollVoteConfig();
            $voteConfig
                ->setCampaignTier($tierEntity)
                ->setPatreonPoll($poll)
                ->setNumberOfVotes($config['voteLimit'])
                ->setVotingPower($config['votePower'])
                ->setMaxOptionAdd($config['addMaxOption']);
            $this->campaignTierRepository->persist($voteConfig);
        }

        $this->campaignTierRepository->save();



        return $this->redirectToRoute('poll_vote', [
            'poll' => $poll->getId()
        ]);
    }
}
