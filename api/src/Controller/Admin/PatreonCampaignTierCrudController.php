<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaignTier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonCampaignTierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonCampaignTier::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('tierName'),
            AssociationField::new('campaign'),
            TextField::new('patreonTierId'),
            MoneyField::new('amountInCents')
                ->setStoredAsCents()
                ->setCurrency('USD')
        ];
    }
}
