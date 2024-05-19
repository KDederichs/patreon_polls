<?php

namespace App\ApiResource;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\PatreonCampaignTier;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'PatreonCampaignTier',
    stateOptions: new Options(entityClass: PatreonCampaignTier::class)
)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[ApiResource(
    uriTemplate: '/patreon_campaign/{campaignId}/tiers',
    shortName: 'PatreonCampaignTier',
    operations: [new GetCollection()],
    uriVariables: [
        'campaignId' => new Link(toProperty: 'campaign', fromClass: PatreonCampaignApi::class),
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonCampaignTier::class)
)]

class PatreonCampaignTierApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    public ?string $tierName = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PatreonCampaignTierApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PatreonCampaignTierApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getTierName(): ?string
    {
        return $this->tierName;
    }

    public function setTierName(?string $tierName): PatreonCampaignTierApi
    {
        $this->tierName = $tierName;
        return $this;
    }
}
