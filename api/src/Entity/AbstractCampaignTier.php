<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[MappedSuperclass]
abstract class AbstractCampaignTier
{
    #[Id, Column(type: UuidType::NAME)]
    #[Groups(['campaign_tier:read'])]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    #[Groups(['campaign_tier:read'])]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    #[Groups(['campaign_tier:read'])]
    private string $tierName;
    #[Column(options: ['default' => 0])]
    private int $amountInCents = 0;

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

    public function getTierName(): string
    {
        return $this->tierName;
    }

    public function setTierName(string $tierName): self
    {
        $this->tierName = $tierName;
        return $this;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function setAmountInCents(int $amountInCents): self
    {
        $this->amountInCents = $amountInCents;
        return $this;
    }

    public function __toString(): string
    {
        return $this->tierName;
    }

    abstract function getOwner(): User;
    abstract function getVoteConfigClass(): string;
}
