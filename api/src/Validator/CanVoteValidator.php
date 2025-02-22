<?php

namespace App\Validator;

use App\ApiResource\PollOptionApi;
use App\ApiResource\PollVoteApi;
use App\Entity\AbstractVoteConfig;
use App\Entity\Poll;
use App\Entity\User;
use App\Mapper\AbstractApiToObjectMapper;
use App\Repository\PollOptionRepository;
use App\Repository\PollVoteRepository;
use App\Service\VoteConfigService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class CanVoteValidator extends ConstraintValidator
{

    public function __construct(
        private readonly Security $security,
        private readonly VoteConfigService $configService,
        private readonly MicroMapperInterface $microMapper,
        private readonly PollVoteRepository $voteRepository,
    )
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CanVote) {
            throw new UnexpectedTypeException($constraint, CanVote::class);
        }

        if (!$value instanceof PollVoteApi) {
            throw new UnexpectedTypeException($value, PollVoteApi::class);
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        /** @var Poll $poll */
        $poll = $this->microMapper->map($value->getPoll(), Poll::class, [
            'mode' => AbstractApiToObjectMapper::POPULATION_MODE_PASSTHROUGH,
        ]);

        if ($endsAt = $poll->getEndsAt()) {
            if ($endsAt->isPast()) {
                $this->context->addViolation('The poll has ended.');
            }
        }

        if ($poll->getCreatedBy()->getId()->equals($user->getId())) {
            return;
        }

        /** @var AbstractVoteConfig $config */
        $config = $this->configService->getConfigForUser($poll, $user);

        if (!$config?->isLimitedVotes()) {
            return;
        }

        $myVotes = $this->voteRepository->getNumberOfVotesForPoll($user, $poll);

        if ($myVotes >= $config->getNumberOfVotes()) {
            $this->context->addViolation(sprintf('You can only vote %s times.', $config->getNumberOfVotes()));
        }
    }
}
