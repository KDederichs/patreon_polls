<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PatreonCampaignWebhook;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PatreonCampaignWebhook>
 */
final class PatreonCampaignWebhookFactory extends PersistentProxyObjectFactory
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
        return PatreonCampaignWebhook::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'campaign' => PatreonCampaignFactory::new(),
            'patreonWebhookId' => self::faker()->text(64),
            'secret' => self::faker()->text(),
            'triggers' => [],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(PatreonCampaignWebhook $patreonCampaignWebhook): void {})
    }
}
