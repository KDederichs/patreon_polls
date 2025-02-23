<?php

namespace App\Controller\Admin;

use App\Entity\MemberEntitledTier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class MemberEntitledTierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MemberEntitledTier::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('campaignMember'),
            AssociationField::new('tier'),
        ];
    }

}
