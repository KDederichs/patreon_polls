<?php

namespace App\Security\Http;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    public function __construct(
        private readonly ApiTokenRepository $apiTokenRepository
    )
    {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $user = $token->getUser();
        assert($user instanceof User);

        $apiToken = $this->apiTokenRepository->createForUser($user);

        return new JsonResponse([
            'token' => $apiToken->getToken(),
        ]);
    }
}
