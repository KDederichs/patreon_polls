<?php

namespace App\Entity;

use App\Repository\PollRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PollRepository::class)]
class Poll
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    private string $pollName;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $endsAt = null;
    #[ManyToOne(targetEntity: User::class)]
    private ?User $createdBy = null;
    #[Column(options: ['default' => false])]
    private bool $allowPictures = false;

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

    public function getPollName(): string
    {
        return $this->pollName;
    }

    public function setPollName(string $pollName): Poll
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndsAt(): ?CarbonImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?CarbonImmutable $endsAt): Poll
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->pollName;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): Poll
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function isAllowPictures(): bool
    {
        return $this->allowPictures;
    }

    public function setAllowPictures(bool $allowPictures): Poll
    {
        $this->allowPictures = $allowPictures;
        return $this;
    }
}
