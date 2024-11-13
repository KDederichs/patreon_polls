<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PatreonCampaign;
use App\Entity\User;
use App\Message\FetchCampaignMembersMessage;
use App\Repository\PatreonUserRepository;
use App\Service\PatreonService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<void, void>
 */
class SyncPatreonCampaignsProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly PatreonService $patreonService,
        private readonly MessageBusInterface $bus,
        private readonly Security $security,
        private readonly PatreonUserRepository $patreonUserRepository,
    )
    {

    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        assert($user instanceof User);
        $patreonUser = $this->patreonUserRepository->findByPatreonId($user->getPatreonId(),true);
        if (!$patreonUser) {
            throw new AccessDeniedHttpException('You are not a patreon creator');
        }

        /** @var PatreonCampaign[] $campaigns */
        $campaigns = $this->patreonService->refreshCampaigns($patreonUser);

        foreach ($campaigns as $campaign) {
            $this->bus->dispatch(new FetchCampaignMembersMessage($campaign->getId()));
            $this->patreonService->enableMemberUpdateWebhook($campaign);
        }
    }
}
