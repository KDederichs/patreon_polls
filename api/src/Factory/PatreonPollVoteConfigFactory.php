<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PatreonPollVoteConfig;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PatreonPollVoteConfig>
 */
final class PatreonPollVoteConfigFactory extends PersistentProxyObjectFactory
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
        return PatreonPollVoteConfig::class;
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
            'campaignTier' => PatreonCampaignTierFactory::new(),
            'limitedVotes' => self::faker()->boolean(),
            'poll' => PollFactory::new(),
            'votingPower' => self::faker()->randomNumber(),
            'maxOptionAdd ' => self::faker()->randomNumber(),
            'numberOfVotes' => self::faker()->randomNumber(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(PatreonPollVoteConfig $patreonPollVoteConfig): void {})
    }
}
