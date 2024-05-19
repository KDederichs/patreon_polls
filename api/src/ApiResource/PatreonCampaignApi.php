<?php

namespace App\ApiResource;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\PatreonCampaign;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'PatreonCampaign',
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonCampaign::class)
)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[GetCollection]
#[Post(
    uriTemplate: 'patreon_campaigns/sync',
    status: 204,
    input: false,
    output: false
)]
class PatreonCampaignApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    public ?string $campaignName = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PatreonCampaignApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PatreonCampaignApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCampaignName(): ?string
    {
        return $this->campaignName;
    }

    public function setCampaignName(?string $campaignName): PatreonCampaignApi
    {
        $this->campaignName = $campaignName;
        return $this;
    }
}
