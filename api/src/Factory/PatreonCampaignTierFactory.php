<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PatreonCampaignTier;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PatreonCampaignTier>
 */
final class PatreonCampaignTierFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return PatreonCampaignTier::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'amountInCents' => self::faker()->randomNumber(),
            'campaign' => PatreonCampaignFactory::new(),
            'patreonTierId' => self::faker()->text(64),
            'tierName' => self::faker()->text(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(PatreonCampaignTier $patreonCampaignTier): void {})
    }
}
