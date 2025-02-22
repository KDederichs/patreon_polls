<?php

namespace App\Controller;

use App\Repository\ApiTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{

    #[Route('/api/logout', name: 'logout', methods: ['DELETE'])]
    public function logout(Request $request, ApiTokenRepository $apiTokenRepository): Response
    {
        $bearerString = $request->headers->get('Authorization');
        if ($bearerString) {
            $accessToken = explode(' ', $bearerString)[1];
            $apiTokenRepository->deleteToken($accessToken);
        }

        return new Response();
    }
}
