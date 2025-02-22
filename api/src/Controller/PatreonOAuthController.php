<?php

namespace App\Controller;

use App\Dto\OAuth\OAuthConnectPayload;
use App\Dto\OAuth\RequestOAuthConnectPayload;
use App\Entity\OauthState;
use App\Enum\OAuthAuthType;
use App\Enum\OAuthProvider;
use App\Repository\OAuthStateRepository;
use App\Service\Oauth\PatreonOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PatreonOAuthController extends AbstractController
{
    public function __construct(
        private readonly PatreonOAuthService  $patreonOAuth,
        private readonly OAuthStateRepository $authStateRepository,
        private readonly Security $security
    )
    {

    }

    #[Route('/login/patreon', methods: ['GET'])]
    public function patreonLogin(): Response
    {
        $authState = new OauthState();
        $authState
            ->setAuthType(OAuthAuthType::Login)
            ->setProvider(OAuthProvider::Patreon);

        $this->authStateRepository->persist($authState);
        $this->authStateRepository->save();


        return new RedirectResponse($this->patreonOAuth->getOauthUrl($authState));
    }

    #[Route('/connect/patreon', methods: ['POST'])]
    public function patreonConnect(#[MapRequestPayload] RequestOAuthConnectPayload $payload): Response
    {

        $mode = $payload->getMode();

        $authState = new OauthState();
        $authState
            ->setUser($this->security->getUser())
            ->setAuthType($mode === 'creator' ? OAuthAuthType::ConnectAsCreator : OAuthAuthType::Connect)
            ->setProvider(OAuthProvider::Patreon);

        $this->authStateRepository->persist($authState);
        $this->authStateRepository->save();

        return new JsonResponse([
            'redirectUri' => $this->patreonOAuth->getOauthUrl($authState)
        ]);
    }
}
