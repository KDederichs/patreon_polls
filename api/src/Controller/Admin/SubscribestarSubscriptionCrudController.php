<?php

namespace App\Controller\Admin;

use App\Entity\SubscribestarSubscription;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubscribestarSubscriptionCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return SubscribestarSubscription::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            AssociationField::new('subscribestarUser'),
            TextField::new('subscribestarId'),
            TextField::new('tierId'),
            TextField::new('contentProviderId'),
            BooleanField::new('active'),
            AssociationField::new('subscribedTo'),
            AssociationField::new('subscribestarTier'),
        ];
    }
}
