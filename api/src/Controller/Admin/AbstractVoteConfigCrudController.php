<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

abstract class AbstractVoteConfigCrudController extends AbstractCrudController
{

    protected abstract function getTierAssociation(): string;

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt')->setTimezone('Europe/Berlin')->hideOnForm(),
            AssociationField::new('poll'),
            AssociationField::new($this->getTierAssociation()),
            BooleanField::new('limitedVotes'),
            IntegerField::new('numberOfVotes'),
            BooleanField::new('addOptions'),
            IntegerField::new('maxOptionAdd'),
            IntegerField::new('votingPower'),
        ];
    }
}
