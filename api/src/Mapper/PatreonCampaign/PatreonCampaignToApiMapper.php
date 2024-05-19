<?php

namespace App\Mapper\PatreonCampaign;

use App\ApiResource\PatreonCampaignApi;
use App\Entity\PatreonCampaign;
use App\Mapper\AbstractObjectToApiMapper;
use Symfonycasts\MicroMapper\AsMapper;

#[AsMapper(from: PatreonCampaign::class, to: PatreonCampaignApi::class)]
class PatreonCampaignToApiMapper extends AbstractObjectToApiMapper
{

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof PatreonCampaign);

        return new PatreonCampaignApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof PatreonCampaign);
        assert($dto instanceof PatreonCampaignApi);
        return $dto
            ->setCampaignName($entity->getCampaignName())
            ->setId($entity->getId())
            ->setCreatedAt($entity->getCreatedAt());
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        // TODO: Implement populateExternal() method.
    }
}
