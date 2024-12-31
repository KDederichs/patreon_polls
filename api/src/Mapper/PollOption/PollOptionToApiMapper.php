<?php

namespace App\Mapper\PollOption;

use App\ApiResource\PollApi;
use App\ApiResource\PollOptionApi;
use App\Entity\PollOption;
use App\Mapper\AbstractObjectToApiMapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PollOption::class, to: PollOptionApi::class)]
final class PollOptionToApiMapper extends AbstractObjectToApiMapper
{

    public function __construct(
        private readonly MicroMapperInterface $microMapper,
        private readonly RouterInterface $router,
    )
    {

    }

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof PollOption);

        return new PollOptionApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof PollOption);
        assert($dto instanceof PollOptionApi);

        if ($entity->getMediaObject()) {
            $dto->setImageUri(
                $this->router->generate('get_media_object', [
                    'mediaObject' => $entity->getMediaObject()->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }

        return $dto
            ->setPoll(
                $this->microMapper->map($entity->getPoll(), PollApi::class)
            )
            ->setOptionName($entity->getOptionName())
            ->setId($entity->getId())
            ->setCreatedAt($entity->getCreatedAt())
            ->setNumberOfVotes($entity->getVoteCount());
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        // TODO: Implement populateExternal() method.
    }
}