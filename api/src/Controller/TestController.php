<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\PatreonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(
        UserRepository $userRepository,
        PatreonService $patreonService,
    ): Response
    {
        $user = $userRepository->find(Uuid::fromString('018f9082-09cf-7295-bd67-b15d442fc282'));
        $patreonService->refreshCampaigns($user);
        return new Response();
    }
}
