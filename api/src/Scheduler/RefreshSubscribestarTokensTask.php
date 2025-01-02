<?php

namespace App\Scheduler;

use App\Repository\PatreonUserRepository;
use App\Repository\SubscribestarUserRepository;
use App\Service\PatreonService;
use App\Service\SubscribestarService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 * * * *')]
class RefreshSubscribestarTokensTask
{
    public function __construct(
        private readonly SubscribestarService  $subscribestarService,
        private readonly SubscribestarUserRepository $subscribestarUserRepository
    )
    {

    }

    public function __invoke(): void
    {
        foreach ($this->subscribestarUserRepository->findForTokenRenew() as $user) {
            $this->subscribestarService->refreshAccessToken($user);
        }
    }
}
