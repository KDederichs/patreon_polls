<?php

namespace App\Validator;

use App\ApiResource\PollOptionApi;

use App\Entity\AbstractVoteConfig;
use App\Entity\Poll;
use App\Entity\User;
use App\Mapper\AbstractApiToObjectMapper;
use App\Repository\PollOptionRepository;
use App\Service\VoteConfigService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class CanAddOptionValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly VoteConfigService $configService,
        private readonly MicroMapperInterface $microMapper,
        private readonly PollOptionRepository $optionRepository,
    )
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CanAddOption) {
            throw new UnexpectedTypeException($constraint, CanAddOption::class);
        }

        if (!$value instanceof PollOptionApi) {
            throw new UnexpectedTypeException($value, PollOptionApi::class);
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

        if (!$config->isAddOptions()) {
            $this->context->addViolation('Adding options for this poll is not enabled.');
        }

        if ($value->getImage() && !$poll->isAllowPictures()) {
            $this->context->addViolation('Adding pictures to poll options is not allowed.');
        }

        if (!$config->isAddOptions()) {
            return;
        }

        $myVotes = $this->optionRepository->getNumberOfMyOptions($poll, $user);
        if ($myVotes >= $config->getMaxOptionAdd()) {
            $this->context->addViolation(sprintf('You can not add more than %s options.', $config->getMaxOptionAdd()));
        }
    }
}
