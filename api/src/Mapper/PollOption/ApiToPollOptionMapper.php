<?php

namespace App\Mapper\PollOption;

use App\ApiResource\PollOptionApi;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\User;
use App\Mapper\AbstractApiToObjectMapper;
use App\Repository\PollOptionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PollOptionApi::class, to: PollOption::class)]
final class ApiToPollOptionMapper extends AbstractApiToObjectMapper
{
    public function __construct(
        private readonly PollOptionRepository $optionRepository,
        private readonly MicroMapperInterface $microMapper,
        private readonly Security $security,
    )
    {

    }


    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof PollOptionApi);

        return $dto->getId() ? $this->optionRepository->find($dto->getId()) : new PollOption();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof PollOptionApi);
        assert($entity instanceof PollOption);

        if (!$entity->getPoll()) {
            $entity->setPoll(
                $this->microMapper->map($dto->getPoll(), Poll::class, [
                    'mode' => AbstractApiToObjectMapper::POPULATION_MODE_PASSTHROUGH,
                ])
            );
        }

        if (!$entity->getCreatedBy()) {
            $user = $this->security->getUser();
            assert($user instanceof User);
            $entity->setCreatedBy($user);
        }

        return $entity
            ->setOptionName($dto->getOptionName())
            ->setMediaObject($dto->getImage())
        ;
    }
}
