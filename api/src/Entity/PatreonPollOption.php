<?php

namespace App\Entity;

use App\Repository\PatreonPollOptionRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonPollOptionRepository::class)]
class PatreonPollOption
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: PatreonPoll::class)]
    #[JoinColumn(nullable: false)]
    private PatreonPoll $poll;
    #[Column(type: 'text')]
    private string $optionName;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $createdBy;
    #[OneToOne(targetEntity: MediaObject::class, orphanRemoval: true)]
    private ?MediaObject $mediaObject = null;
    #[OneToMany(targetEntity: PatreonPollVote::class, mappedBy: 'option',fetch: 'EAGER')]
    private Collection $votes;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
        $this->votes = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getPoll(): PatreonPoll
    {
        return $this->poll;
    }

    public function setPoll(PatreonPoll $poll): PatreonPollOption
    {
        $this->poll = $poll;
        return $this;
    }

    public function getOptionName(): string
    {
        return $this->optionName;
    }

    public function setOptionName(string $optionName): PatreonPollOption
    {
        $this->optionName = $optionName;
        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): PatreonPollOption
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getMediaObject(): ?MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(?MediaObject $mediaObject): PatreonPollOption
    {
        $this->mediaObject = $mediaObject;
        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function setVotes(Collection $votes): PatreonPollOption
    {
        $this->votes = $votes;
        return $this;
    }

    public function getVoteCount(): int
    {
        return $this->votes->reduce(
            static fn (int $carryOver, PatreonPollVote $vote) => $carryOver + $vote->getVotePower(),
            0
        ) ?? 0;
    }
}
