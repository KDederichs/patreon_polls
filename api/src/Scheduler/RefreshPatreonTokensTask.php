<?php

namespace App\Scheduler;

use App\Repository\PatreonUserRepository;
use App\Service\PatreonService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 * * * *')]
class RefreshPatreonTokensTask
{
    public function __construct(
        private readonly PatreonService $patreonService,
        private readonly PatreonUserRepository $patreonUserRepository
    )
    {

    }

    public function __invoke(): void
    {
        foreach ($this->patreonUserRepository->findForTokenRenew() as $user) {
            $this->patreonService->refreshAccessToken($user);
        }
    }
}
