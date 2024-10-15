<?php

namespace App\Entity;

use App\Repository\PatreonPollTierVoteConfigRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonPollTierVoteConfigRepository::class)]
#[UniqueConstraint(fields: ['patreonPoll', 'campaignTier'])]
class PollVoteConfig
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: Poll::class)]
    #[JoinColumn(nullable: false)]
    public Poll $patreonPoll;
    #[ManyToOne(targetEntity: PatreonCampaignTier::class)]
    #[JoinColumn(nullable: false)]
    public PatreonCampaignTier $campaignTier;
    #[Column(type: 'smallint')]
    public int $numberOfVotes = 0;
    #[Column(type: 'smallint')]
    public int $votingPower = 1;
    #[Column(type: 'smallint', options: ['default' => 1])]
    public int $maxOptionAdd = 1;

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

    public function getPatreonPoll(): Poll
    {
        return $this->patreonPoll;
    }

    public function setPatreonPoll(Poll $patreonPoll): PollVoteConfig
    {
        $this->patreonPoll = $patreonPoll;
        return $this;
    }

    public function getCampaignTier(): PatreonCampaignTier
    {
        return $this->campaignTier;
    }

    public function setCampaignTier(PatreonCampaignTier $campaignTier): PollVoteConfig
    {
        $this->campaignTier = $campaignTier;
        return $this;
    }

    public function getNumberOfVotes(): int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(int $numberOfVotes): PollVoteConfig
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }

    public function getVotingPower(): int
    {
        return $this->votingPower;
    }

    public function setVotingPower(int $votingPower): PollVoteConfig
    {
        $this->votingPower = $votingPower;
        return $this;
    }

    public function getMaxOptionAdd(): int
    {
        return $this->maxOptionAdd;
    }

    public function setMaxOptionAdd(int $maxOptionAdd): PollVoteConfig
    {
        $this->maxOptionAdd = $maxOptionAdd;
        return $this;
    }
}
