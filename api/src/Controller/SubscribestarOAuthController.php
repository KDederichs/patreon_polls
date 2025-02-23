<?php

namespace App\Controller;

use App\Dto\OAuth\RequestOAuthConnectPayload;
use App\Entity\OauthState;
use App\Enum\OAuthAuthType;
use App\Enum\OAuthProvider;
use App\Repository\OAuthStateRepository;
use App\Service\Oauth\PatreonOAuthService;
use App\Service\Oauth\SubscribestarOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class SubscribestarOAuthController extends AbstractController
{
    public function __construct(
        private readonly SubscribestarOAuthService  $authService,
        private readonly OAuthStateRepository $authStateRepository,
        private readonly Security $security
    )
    {

    }

    #[Route('/login/subscribestar', methods: ['GET'])]
    public function subscribestarLogin(): Response
    {
        $authState = new OauthState();
        $authState
            ->setAuthType(OAuthAuthType::Login)
            ->setProvider(OAuthProvider::SubscribeStar);

        $this->authStateRepository->persist($authState);
        $this->authStateRepository->save();


        return new RedirectResponse(str_replace('%2B','+', $this->authService->getOauthUrl($authState)));
    }

    #[Route('/connect/subscribestar', methods: ['POST'])]
    public function subscribestarConnect(#[MapRequestPayload] RequestOAuthConnectPayload $payload): Response
    {

        $mode = $payload->getMode();

        $authState = new OauthState();
        $authState
            ->setUser($this->security->getUser())
            ->setAuthType($mode === 'creator' ? OAuthAuthType::ConnectAsCreator : OAuthAuthType::Connect)
            ->setProvider(OAuthProvider::SubscribeStar);

        $this->authStateRepository->persist($authState);
        $this->authStateRepository->save();


        return new JsonResponse([
            'redirectUri' => $this->authService->getOauthUrl($authState)
        ]);
    }
}
