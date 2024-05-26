<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        return new Response();
    }
}
