<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SubscribestarTier;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SubscribestarTier>
 */
final class SubscribestarTierFactory extends PersistentProxyObjectFactory
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
        return SubscribestarTier::class;
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
            'subscribestarTierId' => self::faker()->text(64),
            'subscribestarUser' => SubscribestarUserFactory::new(),
            'tierName' => self::faker()->text(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(SubscribestarTier $subscribestarTier): void {})
    }
}