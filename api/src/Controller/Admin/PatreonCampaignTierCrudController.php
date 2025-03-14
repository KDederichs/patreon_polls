<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaignTier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            TextField::new('tierName'),
            MoneyField::new('amountInCents')
                ->setStoredAsCents()
                ->setCurrency('USD'),
            AssociationField::new('campaign'),
            TextField::new('patreonTierId'),
        ];
    }
}
