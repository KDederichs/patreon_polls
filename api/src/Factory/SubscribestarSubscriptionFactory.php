<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SubscribestarSubscription;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SubscribestarSubscription>
 */
final class SubscribestarSubscriptionFactory extends PersistentProxyObjectFactory
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
        return SubscribestarSubscription::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'active' => self::faker()->boolean(),
            'contentProviderId' => self::faker()->text(64),
            'subscribestarId' => self::faker()->text(64),
            'subscribestarUser' => SubscribestarUserFactory::new(),
            'tierId' => self::faker()->text(64),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(SubscribestarSubscription $subscribestarSubscription): void {})
    }
}
