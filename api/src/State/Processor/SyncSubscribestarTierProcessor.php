<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PatreonCampaign;
use App\Entity\User;
use App\Message\FetchCampaignMembersMessage;
use App\Repository\PatreonUserRepository;
use App\Repository\SubscribestarUserRepository;
use App\Service\PatreonService;
use App\Service\SubscribestarService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<void, void>
 */
class SyncSubscribestarTierProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly SubscribestarService $subscribestarService,
        private readonly Security $security,
        private readonly SubscribestarUserRepository $patreonUserRepository,
    )
    {

    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();
        assert($user instanceof User);
        $subscribestarUser = $this->patreonUserRepository->findBySubscribestarId($user->getSubscribestarId(),true);
        if (!$subscribestarUser) {
            throw new AccessDeniedHttpException('You are not a subscribestar creator');
        }

        $this->subscribestarService->refreshTiers($subscribestarUser);
    }
}
