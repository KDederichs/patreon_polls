<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[MappedSuperclass]
abstract class AbstractVoteConfig
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: Poll::class)]
    #[JoinColumn(nullable: false)]
    private Poll $poll;
    #[Column(type: 'smallint')]
    private int $numberOfVotes = 0;
    #[Column(type: 'smallint')]
    private int $votingPower = 1;
    #[Column(type: 'smallint', options: ['default' => 1])]
    private int $maxOptionAdd = 1;
    #[Column(options: ['default' => false])]
    private bool $addOptions = false;
    #[Column(options: ['default' => false])]
    private bool $limitedVotes = false;

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

    public function getPoll(): Poll
    {
        return $this->poll;
    }

    public function setPoll(Poll $poll): AbstractVoteConfig
    {
        $this->poll = $poll;
        return $this;
    }

    public function getNumberOfVotes(): int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(int $numberOfVotes): AbstractVoteConfig
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }

    public function getVotingPower(): int
    {
        return $this->votingPower;
    }

    public function setVotingPower(int $votingPower): AbstractVoteConfig
    {
        $this->votingPower = $votingPower;
        return $this;
    }

    public function getMaxOptionAdd(): int
    {
        return $this->maxOptionAdd;
    }

    public function setMaxOptionAdd(int $maxOptionAdd): AbstractVoteConfig
    {
        $this->maxOptionAdd = $maxOptionAdd;
        return $this;
    }

    public function isAddOptions(): bool
    {
        return $this->addOptions;
    }

    public function setAddOptions(bool $addOptions): AbstractVoteConfig
    {
        $this->addOptions = $addOptions;
        return $this;
    }

    public function isLimitedVotes(): bool
    {
        return $this->limitedVotes;
    }

    public function setLimitedVotes(bool $limitedVotes): AbstractVoteConfig
    {
        $this->limitedVotes = $limitedVotes;
        return $this;
    }
}
