<?php

namespace App\Controller\Admin;

use App\Entity\PollOption;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonPollOptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PollOption::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('poll'),
            TextField::new('optionName'),
            AssociationField::new('createdBy'),
            AssociationField::new('votes'),
        ];
    }
}
