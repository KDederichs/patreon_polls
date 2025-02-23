<?php

namespace App\Controller\Admin;

use App\Entity\PollOption;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PollOptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PollOption::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            AssociationField::new('poll'),
            TextField::new('optionName'),
            AssociationField::new('createdBy'),
            AssociationField::new('mediaObject')->setTemplatePath('admin/media_object_field.html.twig'),
            AssociationField::new('votes'),
        ];
    }
}
