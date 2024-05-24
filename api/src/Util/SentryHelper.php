<?php

namespace App\Util;

use App\Entity\User;
use Sentry\State\Scope;
use function Sentry\configureScope;

class SentryHelper
{
    public static function identifyUser(User $user): void
    {
        configureScope(function (Scope $scope) use ($user): void {
            $scope->setUser([
                'email' => $user->getEmail(),
                'id' => $user->getId()->toRfc4122(),
            ]);
        });
    }

    public static function addContext(string $key, array $context): void
    {
        configureScope(function (Scope $scope) use ($context, $key): void {
            $scope->setContext(
                $key,
                $context
            );
        });
    }
}
