<?php

namespace App\Controller\Admin;

use App\Entity\PatreonPollVoteConfig;
use App\Entity\SubscribestarPollVoteConfig;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubscribestarTierVoteConfigCrudController extends AbstractVoteConfigCrudController
{
    public static function getEntityFqcn(): string
    {
        return SubscribestarPollVoteConfig::class;
    }

    protected function getTierAssociation(): string
    {
        return 'campaignTier';
    }
}
