<?php

namespace App\Mapper\PatreonCampaignTier;

use App\ApiResource\PatreonCampaignTierApi;
use App\Entity\PatreonCampaignTier;
use App\Mapper\AbstractObjectToApiMapper;
use Symfonycasts\MicroMapper\AsMapper;

#[AsMapper(from: PatreonCampaignTier::class, to: PatreonCampaignTierApi::class)]
class PatreonCampaignTierToApiMapper extends AbstractObjectToApiMapper
{

    protected function internalLoader(object $from, string $toClass, array $context): object
    {
       $entity = $from;
       assert($entity instanceof PatreonCampaignTier);

       return new PatreonCampaignTierApi();
    }

    protected function internalPopulate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof PatreonCampaignTier);
        assert($dto instanceof PatreonCampaignTierApi);

        return $dto
            ->setId($entity->getId())
            ->setTierName($entity->getTierName())
            ->setCreatedAt($entity->getCreatedAt());
    }

    protected function populateExternal(object $from, object $to, array $context): void
    {
        // TODO: Implement populateExternal() method.
    }
}
