<?php

namespace App\Security\Http;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Repository\PatreonUserRepository;
use App\Service\PatreonService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class CreatorConnectSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        return new JsonResponse([
            'success' => true,
        ]);
    }
}
