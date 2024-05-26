<?php

namespace App\Controller\Admin;

use App\Entity\PatreonPollVote;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PatreonPollVoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PatreonPollVote::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('poll'),
            AssociationField::new('option'),
            AssociationField::new('votedBy'),
            IntegerField::new('votePower')
        ];
    }

}
