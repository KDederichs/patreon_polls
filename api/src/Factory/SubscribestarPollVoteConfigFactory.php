<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SubscribestarPollVoteConfig;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SubscribestarPollVoteConfig>
 */
final class SubscribestarPollVoteConfigFactory extends PersistentProxyObjectFactory
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
        return SubscribestarPollVoteConfig::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'addOptions' => self::faker()->boolean(),
            'campaignTier' => SubscribestarTierFactory::new(),
            'limitedVotes' => self::faker()->boolean(),
            'poll' => PollFactory::new(),
            'votingPower' => self::faker()->randomNumber(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(SubscribestarPollVoteConfig $subscribestarPollVoteConfig): void {})
    }
}
