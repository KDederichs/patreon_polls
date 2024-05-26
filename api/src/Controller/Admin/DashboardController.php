<?php

namespace App\Controller\Admin;

use App\Entity\MemberEntitledTier;
use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use App\Entity\PatreonPollTierVoteConfig;
use App\Entity\PatreonPollVote;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Poll Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Patreon');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Campaigns', 'fa fa-list', PatreonCampaign::class);
        yield MenuItem::linkToCrud('Campaign Members', 'fa fa-users', PatreonCampaignMember::class);
        yield MenuItem::linkToCrud('Campaign Tiers', 'fa fa-rank-star', PatreonCampaignTier::class);
        yield MenuItem::linkToCrud('Member Entitlements', 'fa fa-lock-open', MemberEntitledTier::class);
        yield MenuItem::linkToCrud('Webhooks', 'fa fa-blog', PatreonCampaignWebhook::class);
        yield MenuItem::section('Polls');
        yield MenuItem::linkToCrud('Polls', 'fa fa-square-poll-vertical', PatreonPoll::class);
        yield MenuItem::linkToCrud('Option', 'fa fa-filter', PatreonPollOption::class);
        yield MenuItem::linkToCrud('Votes', 'fa fa-check-to-slot', PatreonPollVote::class);
        yield MenuItem::linkToCrud('Vote Config', 'fa fa-flask', PatreonPollTierVoteConfig::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
