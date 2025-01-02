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
class SubscribestarPollVoteConfig extends AbstractVoteConfig
{
    #[ManyToOne(targetEntity: SubscribestarTier::class, fetch: 'EAGER')]
    #[JoinColumn(nullable: false)]
    public SubscribestarTier $campaignTier;


    public function getCampaignTier(): SubscribestarTier
    {
        return $this->campaignTier;
    }

    public function setCampaignTier(AbstractCampaignTier $abstractCampaignTier): SubscribestarPollVoteConfig
    {
        assert($abstractCampaignTier instanceof SubscribestarTier);
        $this->campaignTier = $abstractCampaignTier;
        return $this;
    }
}
