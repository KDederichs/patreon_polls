<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\PatreonCampaignTierRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
#[Entity(repositoryClass: PatreonCampaignTierRepository::class)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[ApiResource(
    uriTemplate: '/patreon_campaigns/{id}/tiers',
    operations: [new GetCollection(
        paginationEnabled: false,
        normalizationContext: [
            'groups' => [
                'campaign_tier:read'
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
class PatreonCampaignTier extends AbstractCampaignTier
{
    #[ManyToOne(targetEntity: PatreonCampaign::class, fetch: 'EAGER', inversedBy: 'campaignTiers')]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonTierId;

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

    function getOwner(): User
    {
        return $this->campaign->getUser();
    }

    function getVoteConfigClass(): string
    {
        return PatreonPollVoteConfig::class;
    }
}
