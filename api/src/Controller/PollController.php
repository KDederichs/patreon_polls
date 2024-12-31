<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PollOptionRepository;
use App\Repository\PatreonPollConfigRepository;
use App\Repository\PollVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PollController extends AbstractController
{

    public function __construct(
        private readonly PollOptionRepository            $patreonPollOptionRepository,
    )
    {

    }

    #[Route('/poll/{poll}/download-marbles', name: 'poll_marbles_download')]
    public function downloadMarblesCsv(Poll $poll, #[CurrentUser] User $user): Response
    {
        if (!$poll->getCreatedBy()?->getId()->equals($user->getId())) {
            throw new AccessDeniedHttpException();
        }

        $options = $this->patreonPollOptionRepository->getOptionsForPoll($poll);
        $csvVotes = [];
        /** @var PollOption $option */
        foreach ($options as $option) {
            for ($i = 0; $i < $option->getVoteCount(); $i++) {
                $csvVotes[] = $option->getOptionName();
            }
        }

        $response = new Response(implode("\n",$csvVotes));
        $response->headers->set('Content-Type', 'text/csv');
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            sprintf('%s.csv', $poll->getPollName())
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}
