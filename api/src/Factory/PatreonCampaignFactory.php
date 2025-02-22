<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PatreonCampaign;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PatreonCampaign>
 */
final class PatreonCampaignFactory extends PersistentProxyObjectFactory
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
        return PatreonCampaign::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'campaignName' => self::faker()->text(),
            'campaignOwner' => UserFactory::new(),
            'patreonCampaignId' => self::faker()->text(64),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(PatreonCampaign $patreonCampaign): void {})
    }
}
