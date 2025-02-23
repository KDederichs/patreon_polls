<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaignTier;
use App\Entity\SubscribestarTier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubscribestarTierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SubscribestarTier::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            TextField::new('tierName'),
            MoneyField::new('amountInCents')
                ->setStoredAsCents()
                ->setCurrency('USD'),
            AssociationField::new('subscribestarUser'),
            TextField::new('subscribestarTierId'),
        ];
    }
}
