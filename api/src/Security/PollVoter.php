<?php

namespace App\Security;

use App\ApiResource\PollApi;
use App\Entity\Poll;
use App\Entity\User;
use App\Repository\PatreonUserRepository;
use App\Service\VoteConfigService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class PollVoter extends Voter
{
    const string CREATE = 'create';
    const string VOTE = 'vote';

    public function __construct(
        private readonly PatreonUserRepository $patreonUserRepository,
        private readonly VoteConfigService $configService,
        private readonly MicroMapperInterface $microMapper
    )
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::VOTE])) {
            return false;
        }

        // only vote on `Post` objects
        if ((!($subject instanceof PollApi)) && (!($subject instanceof Poll))) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $poll = $subject;
        if (!$poll instanceof Poll) {
            $poll = $this->microMapper->map($poll, Poll::class);
        }

        return match($attribute) {
            self::CREATE => $this->canCreate($user),
            self::VOTE => $this->canVote($poll, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canVote(?Poll $poll, User $user): bool
    {
        if (!$poll) {
            return false;
        }

        if ($poll->getCreatedBy()?->getId()->equals($user->getId())) {
            return true;
        }
        return $this->configService->getConfigForUser($poll,$user) !== null;
    }

    private function canCreate(User $user): bool
    {
        $patreonUser = $this->patreonUserRepository->findByPatreonId($user->getPatreonId() ?? '', true);
        return $patreonUser !== null;
    }
}
