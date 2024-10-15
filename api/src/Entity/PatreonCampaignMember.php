<?php

namespace App\Entity;

use App\Repository\PatreonCampaignMemberRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignMemberRepository::class)]
#[UniqueConstraint(fields: ['campaign','patreonUserId'])]
class PatreonCampaignMember
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(name: 'patreon_user_id_string', type: 'string', length: 64)]
    private string $patreonUserId;
    #[ManyToOne(targetEntity: PatreonCampaign::class)]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;
    #[OneToMany(targetEntity: MemberEntitledTier::class, mappedBy: 'campaignMember',fetch: 'EAGER')]
    private Collection $entitledTiers;
    #[ManyToOne(targetEntity: PatreonUser::class)]
    #[JoinColumn(referencedColumnName: '')]
    private ?PatreonUser $patreonUser = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
        $this->entitledTiers = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getPatreonUserId(): string
    {
        return $this->patreonUserId;
    }

    public function setPatreonUserId(string $patreonUserId): PatreonCampaignMember
    {
        $this->patreonUserId = $patreonUserId;
        return $this;
    }

    public function getCampaign(): PatreonCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(PatreonCampaign $campaign): PatreonCampaignMember
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getEntitledTiers(): Collection
    {
        return $this->entitledTiers;
    }

    public function setEntitledTiers(Collection $entitledTiers): PatreonCampaignMember
    {
        $this->entitledTiers = $entitledTiers;
        return $this;
    }

    public function getHighestEntitledTier(): ?MemberEntitledTier
    {
        return $this->entitledTiers->reduce(
            function(?MemberEntitledTier $carryOver, MemberEntitledTier $current) {
                if (null === $carryOver) {
                    return $current;
                }

                return $carryOver->getTier()->getAmountInCents() < $current->getTier()->getAmountInCents() ? $current : $carryOver;
            }
        );
    }

    public function __toString(): string
    {
        return $this->patreonUserId;
    }

    public function getPatreonUser(): ?PatreonUser
    {
        return $this->patreonUser;
    }

    public function setPatreonUser(?PatreonUser $patreonUser): PatreonCampaignMember
    {
        $this->patreonUser = $patreonUser;
        return $this;
    }
}
