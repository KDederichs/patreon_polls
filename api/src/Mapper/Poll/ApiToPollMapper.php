<?php

namespace App\Mapper\Poll;

use App\ApiResource\PollApi;
use App\Entity\Poll;
use App\Entity\User;
use App\Mapper\AbstractApiToObjectMapper;
use App\Repository\PollRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;

#[AsMapper(from: PollApi::class, to: Poll::class)]
final class ApiToPollMapper extends AbstractApiToObjectMapper
{

    public function __construct(
        private readonly Security $security,
        private readonly PollRepository $pollRepository
    )
    {

    }

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof PollApi);

        return $dto->getId() ? $this->pollRepository->find($dto->getId()) : new Poll();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof PollApi);
        assert($entity instanceof Poll);

        if (!$entity->getCreatedBy()) {
            $user = $this->security->getUser();
            assert($user instanceof User);
            $entity->setCreatedBy($user);
        }
        return $entity
            ->setPollName($dto->getPollName())
            ->setAllowPictures($dto->isAllowPictures())
            ->setEndsAt($dto->getEndsAt());
    }
}
