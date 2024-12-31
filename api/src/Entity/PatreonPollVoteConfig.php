<?php

namespace App\Entity;

use App\Repository\PatreonPollConfigRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonPollConfigRepository::class)]
#[UniqueConstraint(fields: ['poll', 'campaignTier'])]
class PatreonPollVoteConfig extends AbstractVoteConfig
{
    #[ManyToOne(targetEntity: PatreonCampaignTier::class, fetch: 'EAGER')]
    #[JoinColumn(nullable: false)]
    public PatreonCampaignTier $campaignTier;


    public function getCampaignTier(): PatreonCampaignTier
    {
        return $this->campaignTier;
    }

    public function setCampaignTier(AbstractCampaignTier $abstractCampaignTier): PatreonPollVoteConfig
    {
        $this->campaignTier = $abstractCampaignTier;
        return $this;
    }
}
