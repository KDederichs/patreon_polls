<?php

namespace App\Controller\Admin;

use App\Entity\PatreonPollTierVoteConfig;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonPollTierVoteConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonPollTierVoteConfig::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('patreonPoll'),
            AssociationField::new('campaignTier'),
            IntegerField::new('numberOfVotes'),
            IntegerField::new('votingPower'),
        ];
    }

}
