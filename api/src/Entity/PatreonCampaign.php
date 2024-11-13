<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\PatreonCampaignRepository;
use App\Security\UserOwnedInterface;
use App\State\Processor\SyncPatreonCampaignsProcessor;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => [
            'patreon_campaign:read'
        ]
    ],
    paginationEnabled: false
)]
#[Post(
    uriTemplate: '/patreon_campaigns/sync',
    input: false,
    output: false,
    processor: SyncPatreonCampaignsProcessor::class,
)]
#[GetCollection]
#[Get]
class PatreonCampaign implements UserOwnedInterface
{
    #[Id, Column(type: UuidType::NAME)]
    #[Groups(['patreon_campaign:read'])]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    #[Groups(['patreon_campaign:read'])]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    #[Groups(['patreon_campaign:read'])]
    private string $campaignName;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $campaignOwner;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonCampaignId;
    #[ManyToOne(targetEntity: PatreonUser::class)]
    private ?PatreonUser $owner = null;
    /**
     * @var Collection<PatreonCampaignTier>
     */
    #[OneToMany(targetEntity: PatreonCampaignTier::class, mappedBy: 'campaign', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Link(toProperty: 'campaign')]
    private Collection $campaignTiers;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
        $this->campaignTiers = new ArrayCollection();
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

    public function addCampaignTier(PatreonCampaignTier $campaignTier): self
    {
        if (!$this->campaignTiers->contains($campaignTier)) {
            $campaignTier->setCampaign($this);
            $this->campaignTiers->add($campaignTier);
        }

        return $this;
    }

    public function removeCampaignTier(PatreonCampaignTier $campaignTier): self
    {
        if ($this->campaignTiers->contains($campaignTier)) {
            $this->campaignTiers->removeElement($campaignTier);
        }

        return $this;
    }

    public function getCampaignTiers(): Collection
    {
        return $this->campaignTiers;
    }

    public function setCampaignTiers(Collection $campaignTiers): PatreonCampaign
    {
        $this->campaignTiers = $campaignTiers;
        return $this;
    }

    public function getUser(): User
    {
        return $this->campaignOwner;
    }

    public static function getUserField(): string
    {
        return 'campaignOwner';
    }
}
