<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaignMember;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonCampaignMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonCampaignMember::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('patreonUserId'),
            AssociationField::new('campaign'),
            AssociationField::new('entitledTiers'),
        ];
    }
}
