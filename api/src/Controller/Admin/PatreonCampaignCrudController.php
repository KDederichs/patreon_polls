<?php

namespace App\Controller\Admin;

use App\Entity\PatreonCampaign;
use App\Service\PatreonService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PatreonCampaignCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PatreonService $patreonService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {

    }

    public static function getEntityFqcn(): string
    {
        return PatreonCampaign::class;
    }

    public function syncMembers(AdminContext $context): Response
    {
        $campaign = $context->getEntity()->getInstance();
        assert($campaign instanceof PatreonCampaign);

        $this->patreonService->fetchCampaignMembers($campaign);
        $this->addFlash('success', 'Members have been synced.');

        return new RedirectResponse($this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        );
    }

    public function configureActions(Actions $actions): Actions
    {


        $markAsSpamAction = Action::new('sync', 'Sync Members')
            ->linkToCrudAction('syncMembers');

        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $markAsSpamAction);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('campaignName'),
            AssociationField::new('campaignOwner'),
            TextField::new('patreonCampaignId'),
        ];
    }
}
