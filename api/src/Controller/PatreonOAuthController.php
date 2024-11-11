<?php

namespace App\Controller;

use App\Entity\OauthState;
use App\Enum\OAuthAuthType;
use App\Enum\OAuthProvider;
use App\Repository\OAuthStateRepository;
use App\Service\Oauth\PatreonOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PatreonOAuthController extends AbstractController
{
    public function __construct(
        private readonly PatreonOAuthService  $patreonOAuth,
        private readonly OAuthStateRepository $authStateRepository,
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

    #[Route('/connect/patreon', methods: ['GET'])]
    public function patreonConnect(Request $request): Response
    {

        $mode = $request->get('mode', 'user');

        $authState = new OauthState();
        $authState
            ->setAuthType($mode === 'creator' ? OAuthAuthType::ConnectAsCreator : OAuthAuthType::Connect)
            ->setProvider(OAuthProvider::Patreon);

        $this->authStateRepository->persist($authState);
        $this->authStateRepository->save();


        return new RedirectResponse($this->patreonOAuth->getOauthUrl($authState));
    }
}
