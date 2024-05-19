<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PatreonPollTierVoteConfig 
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: PatreonPoll::class)]
    #[JoinColumn(nullable: false)]
    public PatreonPoll $patreonPoll;
    #[ManyToOne(targetEntity: PatreonPoll::class)]
    #[JoinColumn(nullable: false)]
    public PatreonCampaignTier $campaignTier;
    #[Column(type: 'smallint')]
    public int $numberOfVotes = 0;

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

    public function getPatreonPoll(): PatreonPoll
    {
        return $this->patreonPoll;
    }

    public function setPatreonPoll(PatreonPoll $patreonPoll): PatreonPollTierVoteConfig
    {
        $this->patreonPoll = $patreonPoll;
        return $this;
    }

    public function getCampaignTier(): PatreonCampaignTier
    {
        return $this->campaignTier;
    }

    public function setCampaignTier(PatreonCampaignTier $campaignTier): PatreonPollTierVoteConfig
    {
        $this->campaignTier = $campaignTier;
        return $this;
    }

    public function getNumberOfVotes(): int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(int $numberOfVotes): PatreonPollTierVoteConfig
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }
}
