<?php

namespace App\Scheduler;

use App\Repository\SubscribestarUserRepository;
use App\Service\SubscribestarService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 0 2 * *')]
class RecheckSubscribestarScheduler
{

    public function __construct(
        private readonly SubscribestarService $subscribestarService,
        private readonly SubscribestarUserRepository $subscribestarUserRepository,
    )
    {

    }

    public function __invoke(): void
    {
        foreach ($this->subscribestarUserRepository->findAll() as $user) {
            $this->subscribestarService->getSubscriptions($user);
            $this->subscribestarUserRepository->getEntityManager()->detach($user);
        }
    }
}
