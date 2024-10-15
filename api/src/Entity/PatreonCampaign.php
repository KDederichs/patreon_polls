<?php

namespace App\Entity;

use App\Repository\PatreonCampaignRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignRepository::class)]
class PatreonCampaign
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    private string $campaignName;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $campaignOwner;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonCampaignId;
    #[ManyToOne(targetEntity: PatreonUser::class)]
    private ?PatreonUser $owner = null;

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

    public function getCampaignName(): string
    {
        return $this->campaignName;
    }

    public function setCampaignName(string $campaignName): PatreonCampaign
    {
        $this->campaignName = $campaignName;
        return $this;
    }

    public function getCampaignOwner(): User
    {
        return $this->campaignOwner;
    }

    public function setCampaignOwner(User $campaignOwner): PatreonCampaign
    {
        $this->campaignOwner = $campaignOwner;
        return $this;
    }

    public function getPatreonCampaignId(): string
    {
        return $this->patreonCampaignId;
    }

    public function setPatreonCampaignId(string $patreonCampaignId): PatreonCampaign
    {
        $this->patreonCampaignId = $patreonCampaignId;
        return $this;
    }

    public function __toString(): string
    {
        return $this->campaignName;
    }

    public function getOwner(): ?PatreonUser
    {
        return $this->owner;
    }

    public function setOwner(?PatreonUser $owner): PatreonCampaign
    {
        $this->owner = $owner;
        return $this;
    }


}
