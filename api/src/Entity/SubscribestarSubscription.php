<?php

namespace App\Entity;

use App\Repository\SubscribestarSubscriptionRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: SubscribestarSubscriptionRepository::class)]
class SubscribestarSubscription
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: SubscribestarUser::class)]
    #[JoinColumn(nullable: false)]
    private SubscribestarUser $subscribestarUser;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $subscribestarId;
    #[Column(type: 'string', length: 64)]
    private string $tierId;
    #[Column(type: 'string', length: 64)]
    private string $contentProviderId;
    #[Column]
    private bool $active;
    #[ManyToOne(targetEntity: SubscribestarUser::class)]
    private ?SubscribestarUser $subscribedTo = null;
    #[ManyToOne(targetEntity: SubscribestarTier::class)]
    private ?SubscribestarTier $subscribestarTier = null;

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


    public function getSubscribestarUser(): SubscribestarUser
    {
        return $this->subscribestarUser;
    }

    public function setSubscribestarUser(SubscribestarUser $subscribestarUser): SubscribestarSubscription
    {
        $this->subscribestarUser = $subscribestarUser;
        return $this;
    }

    public function getSubscribestarId(): string
    {
        return $this->subscribestarId;
    }

    public function setSubscribestarId(string $subscribestarId): SubscribestarSubscription
    {
        $this->subscribestarId = $subscribestarId;
        return $this;
    }

    public function getTierId(): string
    {
        return $this->tierId;
    }

    public function setTierId(string $tierId): SubscribestarSubscription
    {
        $this->tierId = $tierId;
        return $this;
    }

    public function getContentProviderId(): string
    {
        return $this->contentProviderId;
    }

    public function setContentProviderId(string $contentProviderId): SubscribestarSubscription
    {
        $this->contentProviderId = $contentProviderId;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): SubscribestarSubscription
    {
        $this->active = $active;
        return $this;
    }

    public function getSubscribestarTier(): ?SubscribestarTier
    {
        return $this->subscribestarTier;
    }

    public function setSubscribestarTier(?SubscribestarTier $subscribestarTier): SubscribestarSubscription
    {
        $this->subscribestarTier = $subscribestarTier;
        return $this;
    }

    public function getSubscribedTo(): ?SubscribestarUser
    {
        return $this->subscribedTo;
    }

    public function setSubscribedTo(?SubscribestarUser $subscribedTo): SubscribestarSubscription
    {
        $this->subscribedTo = $subscribedTo;
        return $this;
    }
}
