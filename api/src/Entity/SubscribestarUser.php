<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\SubscribestarUserRepository;
use Doctrine\ORM\Mapping\Entity;

#[ApiResource(
    normalizationContext: [
        'groups' => [
            'oauth:read'
        ]
    ]
)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[GetCollection]
#[Entity(repositoryClass: SubscribestarUserRepository::class)]
class SubscribestarUser extends OauthResource
{

}
