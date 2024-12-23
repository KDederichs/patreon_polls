<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\OauthState;
use App\Enum\OAuthAuthType;
use App\Enum\OAuthProvider;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<OauthState>
 */
final class OauthStateFactory extends PersistentProxyObjectFactory
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
        return OauthState::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'authType' => self::faker()->randomElement(OAuthAuthType::cases()),
            'provider' => self::faker()->randomElement(OAuthProvider::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function(OauthState $oauthState): void {})
    }
}
