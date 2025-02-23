<?php

namespace App\Controller\Admin;

use App\Entity\PatreonPollVoteConfig;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonTierVoteConfigCrudController extends AbstractVoteConfigCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonPollVoteConfig::class;
    }

    protected function getTierAssociation(): string
    {
        return 'campaignTier';
    }
}
