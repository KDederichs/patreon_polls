<?php

namespace App\Controller\Admin;

use App\Entity\SubscribestarUser;

class SubscribestarUserCrudController extends AbstractOauthResourceCrudController
{

    public static function getEntityFqcn(): string
    {
        return SubscribestarUser::class;
    }
}
