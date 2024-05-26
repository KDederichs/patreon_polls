<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaign;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonCampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonCampaign::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('campaignName'),
            AssociationField::new('campaignOwner'),
            TextField::new('patreonCampaignId'),
        ];
    }
}
