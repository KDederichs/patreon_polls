<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PatreonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PatreonController extends AbstractController
{
    #[Route('/patreon/convert-creator', name: 'convert_to_creator_account', methods: ['GET', 'POST'])]
    public function convertToCreatorAccount(
        Request $request,
        PatreonService $patreonService,
        #[CurrentUser] User $user,
    ): Response
    {
        $form = $this->createFormBuilder()
            ->add('consent', SubmitType::class, [
                'label' => 'I understand, please convert my account',
                'attr' => [
                    'class' => 'rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $patreonService->convertToCreatorAccount($user);
            return $this->redirectToRoute('poll_create');
        }

        return $this->render('convert_creator_account.html.twig', ['form' => $form]);
    }

    #[Route('/patreon/sync', name: 'sync_patreon', methods: ['GET'])]
    public function syncPatreon(
        PatreonService $patreonService,
        #[CurrentUser] User $user,
    ): Response
    {
        $patreonService->syncPatreon($user);
        $this->addFlash('success', 'Syncing has started. Please wait a minute or so till the import is finished.');
        return $this->redirectToRoute('poll_index');
    }
}
