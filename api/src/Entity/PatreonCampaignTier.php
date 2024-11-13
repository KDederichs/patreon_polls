<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\PatreonCampaignTierRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignTierRepository::class)]
#[ApiResource(
    uriTemplate: '/patreon_campaigns/{id}/tiers',
    operations: [new GetCollection(
        paginationEnabled: false,
        normalizationContext: [
            'groups' => [
                'patreon_campaign_tier:read'
            ]
        ]
    )],
    uriVariables: [
        'id' => new Link(
            toProperty: 'campaign',
            fromClass: PatreonCampaign::class,
            security: "campaign.getCampaignOwner() == user"
        )
    ]
)]
class PatreonCampaignTier
{
    #[Id, Column(type: UuidType::NAME)]
    #[Groups(['patreon_campaign_tier:read'])]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    #[Groups(['patreon_campaign_tier:read'])]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    #[Groups(['patreon_campaign_tier:read'])]
    private string $tierName;
    #[ManyToOne(targetEntity: PatreonCampaign::class, inversedBy: 'campaignTiers')]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonTierId;
    #[Column(options: ['default' => 0])]
    private int $amountInCents = 0;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getTierName(): string
    {
        return $this->tierName;
    }

    public function setTierName(string $tierName): PatreonCampaignTier
    {
        $this->tierName = $tierName;
        return $this;
    }

    public function getCampaign(): PatreonCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(PatreonCampaign $campaign): PatreonCampaignTier
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getPatreonTierId(): string
    {
        return $this->patreonTierId;
    }

    public function setPatreonTierId(string $patreonTierId): PatreonCampaignTier
    {
        $this->patreonTierId = $patreonTierId;
        return $this;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function setAmountInCents(int $amountInCents): PatreonCampaignTier
    {
        $this->amountInCents = $amountInCents;
        return $this;
    }

    public function __toString(): string
    {
        return $this->tierName;
    }
}
