<?php

namespace App\MessageHandler;

use App\Message\RefreshSubscribestarSubscriptionsMessage;
use App\Repository\SubscribestarUserRepository;
use App\Service\SubscribestarService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RefreshSubscribestarSubscriptionsHandler
{
    public function __construct(
        private readonly SubscribestarUserRepository $subscribestarUserRepository,
        private readonly SubscribestarService $subscribestarService,
    )
    {

    }

    public function __invoke(RefreshSubscribestarSubscriptionsMessage $message): void
    {
        $subscribestarUser = $this->subscribestarUserRepository->find($message->subscribestarUserId);
        if (null === $subscribestarUser) {
            return;
        }

        $this->subscribestarService->getSubscriptions($subscribestarUser);
    }
}
