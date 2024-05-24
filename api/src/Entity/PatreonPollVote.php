<?php

namespace App\Entity;

use App\Repository\PatreonPollVoteRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonPollVoteRepository::class)]
#[UniqueConstraint(fields: ['option', 'votedBy'])]
class PatreonPollVote
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: PatreonPoll::class)]
    #[JoinColumn(nullable: false)]
    private PatreonPoll $poll;
    #[ManyToOne(targetEntity: PatreonPollOption::class, inversedBy: 'votes')]
    #[JoinColumn(nullable: false)]
    private PatreonPollOption $option;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $votedBy;
    #[Column(type: 'smallint', options: ['default' => 1])]
    private int $votePower = 1;


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

    public function getOption(): PatreonPollOption
    {
        return $this->option;
    }

    public function setOption(PatreonPollOption $option): PatreonPollVote
    {
        $this->option = $option;
        return $this;
    }

    public function getVotedBy(): User
    {
        return $this->votedBy;
    }

    public function setVotedBy(User $votedBy): PatreonPollVote
    {
        $this->votedBy = $votedBy;
        return $this;
    }

    public function getPoll(): PatreonPoll
    {
        return $this->poll;
    }

    public function setPoll(PatreonPoll $poll): PatreonPollVote
    {
        $this->poll = $poll;
        return $this;
    }

    public function getVotePower(): int
    {
        return $this->votePower;
    }

    public function setVotePower(int $votePower): PatreonPollVote
    {
        $this->votePower = $votePower;
        return $this;
    }
}
