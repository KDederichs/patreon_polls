<?php

namespace App\Mapper\Poll;

use ApiPlatform\Metadata\Get;
use App\ApiResource\PollApi;
use App\Dto\PollVoteConfigDto;
use App\Entity\Poll;
use App\Entity\User;
use App\Mapper\AbstractObjectToApiMapper;
use App\Service\VoteConfigService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;

#[AsMapper(from: Poll::class, to: PollApi::class)]
final class PollToApiMapper extends AbstractObjectToApiMapper
{

    public function __construct(
        private readonly Security $security,
        private readonly VoteConfigService $configService,
    )
    {

    }

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Poll);

        return new PollApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Poll);
        assert($dto instanceof PollApi);

        return $dto
            ->setId($entity->getId())
            ->setCreatedAt($entity->getCreatedAt())
            ->setEndsAt($entity->getEndsAt()?->toDateTimeImmutable())
            ->setAllowPictures($entity->isAllowPictures())
            ->setPollName($entity->getPollName())
        ;
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        if (!isset($context['operation']) || !($context['operation'] instanceof Get)) {
            return;
        }
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Poll);
        assert($dto instanceof PollApi);
        $pollConfig = null;
        if ($user = $this->security->getUser()) {
            assert($user instanceof User);
            if ($entity->getCreatedBy()?->getId()->equals($user->getId())) {
                $dto->setMyPoll(true);
                $pollConfig = new PollVoteConfigDto();
                $pollConfig
                    ->setVotingPower(1)
                    ->setCanAddOptions(true)
                    ->setHasLimitedVotes(false)
                    ->setNumberOfOptions(999);
            } else {
                $config = $this->configService->getConfigForUser($entity, $user);
                if ($config) {
                    $pollConfig = new PollVoteConfigDto();
                    $pollConfig
                        ->setVotingPower($config->getVotingPower())
                        ->setNumberOfVotes($config->getNumberOfVotes())
                        ->setCanAddOptions($config->isAddOptions())
                        ->setHasLimitedVotes($config->isLimitedVotes())
                        ->setNumberOfOptions($config->getMaxOptionAdd());
                }
            }
        }
        $dto->setVoteConfigDto($pollConfig);
    }
}
