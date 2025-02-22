<?php

namespace App\Repository;

use App\Entity\OauthResource;

interface ResourceOwnedInterface
{
    public function getOAuthResource(string $resourceId): ?OauthResource;
}
