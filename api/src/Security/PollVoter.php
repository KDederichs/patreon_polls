<?php

namespace App\Security;

use App\Dto\CreatePollInput;
use App\Entity\Poll;
use App\Entity\User;
use App\Repository\PatreonUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PollVoter extends Voter
{
    const string CREATE = 'create';
    const string VOTE = 'vote';

    public function __construct(
        private readonly PatreonUserRepository $patreonUserRepository
    )
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::VOTE])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof CreatePollInput) {
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
        /** @var CreatePollInput $poll */
        $poll = $subject;



        return match($attribute) {
            self::CREATE => $this->canCreate($user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canCreate(User $user): bool
    {
        $patreonUser = $this->patreonUserRepository->findByPatreonId($user->getPatreonId() ?? '', true);
        return $patreonUser !== null;
    }
}
