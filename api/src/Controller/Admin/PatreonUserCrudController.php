<?php

namespace App\Controller\Admin;

use App\Entity\PatreonUser;

class PatreonUserCrudController extends AbstractOauthResourceCrudController
{

    public static function getEntityFqcn(): string
    {
        return PatreonUser::class;
    }
}
