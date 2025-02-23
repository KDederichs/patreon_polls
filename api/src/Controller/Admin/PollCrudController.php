<?php

namespace App\Controller\Admin;

use App\Entity\Poll;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PollCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Poll::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            TextField::new('pollName'),
            DateTimeField::new('endsAt')->setTimezone('Europe/Berlin'),
            AssociationField::new('createdBy'),
            BooleanField::new('allowPictures')
        ];
    }

}
