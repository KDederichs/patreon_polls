<?php

namespace App\Controller;

use App\Repository\ApiTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AccessTokenController extends AbstractController
{
    #[Route('/login/token/create', name: 'create_poll_token')]
    public function getToken(
        Security $security,
        ApiTokenRepository $apiTokenRepository,
    ): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse([
            'access_token' => $apiTokenRepository->createForUser($user)->getToken()
        ]);
    }
}
