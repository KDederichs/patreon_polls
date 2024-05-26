<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaignWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonCampaignWebhookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonCampaignWebhook::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('patreonWebhookId'),
            ArrayField::new('triggers'),
            AssociationField::new('campaign'),
            TextField::new('secret')
        ];
    }

}
