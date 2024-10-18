<?php

namespace App\Entity;

use App\Repository\PatreonUserRepository;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: PatreonUserRepository::class)]
class PatreonUser extends OauthResource
{

}
