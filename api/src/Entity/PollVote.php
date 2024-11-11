<?php

namespace App\Entity;

use App\Repository\PatreonPollVoteRepository;
use App\Security\UserOwnedInterface;
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
class PollVote implements UserOwnedInterface
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: Poll::class)]
    #[JoinColumn(nullable: false)]
    private Poll $poll;
    #[ManyToOne(targetEntity: PollOption::class, inversedBy: 'votes')]
    #[JoinColumn(nullable: false)]
    private PollOption $option;
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

    public function getOption(): PollOption
    {
        return $this->option;
    }

    public function setOption(PollOption $option): PollVote
    {
        $this->option = $option;
        return $this;
    }

    public function getVotedBy(): User
    {
        return $this->votedBy;
    }

    public function setVotedBy(User $votedBy): PollVote
    {
        $this->votedBy = $votedBy;
        return $this;
    }

    public function getPoll(): Poll
    {
        return $this->poll;
    }

    public function setPoll(Poll $poll): PollVote
    {
        $this->poll = $poll;
        return $this;
    }

    public function getVotePower(): int
    {
        return $this->votePower;
    }

    public function setVotePower(int $votePower): PollVote
    {
        $this->votePower = $votePower;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s->%s->%s', $this->poll->getPollName(), $this->option->getOptionName(), $this->votedBy->getUsername());
    }

    public function getUser(): User
    {
        return $this->getVotedBy();
    }

    public static function getUserField(): string
    {
        return 'votedBy';
    }
}
