<?php

namespace App\Controller\Admin;

use App\Entity\Poll;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonPollCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Poll::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('pollName'),
            AssociationField::new('campaign'),
        ];
    }

}
