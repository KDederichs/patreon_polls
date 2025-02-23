<?php

namespace App\Controller\Admin;

use App\Entity\PollVote;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PollVoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PollVote::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            AssociationField::new('poll'),
            AssociationField::new('option'),
            AssociationField::new('votedBy'),
            IntegerField::new('votePower')
        ];
    }

}
