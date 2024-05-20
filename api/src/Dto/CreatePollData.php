<?php

namespace App\Dto;

use App\Entity\PatreonCampaign;
use Doctrine\Common\Collections\ArrayCollection;

class CreatePollData
{
    private ?PatreonCampaign $patreonCampaign = null;
    private array|ArrayCollection|null $votingTiers = null;
    private array|ArrayCollection|null $votingPower = null;
    private array|ArrayCollection|null $voteLimit = null;
    private ?string $pollName = null;
    private ?\DateTimeImmutable $endDate = null;

    public function getPatreonCampaign(): ?PatreonCampaign
    {
        return $this->patreonCampaign;
    }

    public function setPatreonCampaign(?PatreonCampaign $patreonCampaign): CreatePollData
    {
        $this->patreonCampaign = $patreonCampaign;
        return $this;
    }


    public function getPollName(): ?string
    {
        return $this->pollName;
    }

    public function setPollName(?string $pollName): CreatePollData
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): CreatePollData
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getVotingTiers(): ArrayCollection|array|null
    {
        return $this->votingTiers;
    }

    public function setVotingTiers(ArrayCollection|array|null $votingTiers): CreatePollData
    {
        $this->votingTiers = $votingTiers;
        return $this;
    }

    public function getVotingPower(): ArrayCollection|array|null
    {
        return $this->votingPower;
    }

    public function setVotingPower(ArrayCollection|array|null $votingPower): CreatePollData
    {
        $this->votingPower = $votingPower;
        return $this;
    }

    public function getVoteLimit(): ArrayCollection|array|null
    {
        return $this->voteLimit;
    }

    public function setVoteLimit(ArrayCollection|array|null $voteLimit): CreatePollData
    {
        $this->voteLimit = $voteLimit;
        return $this;
    }







}
