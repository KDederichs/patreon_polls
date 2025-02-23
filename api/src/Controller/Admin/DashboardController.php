<?php

namespace App\Controller\Admin;

use App\Entity\MemberEntitledTier;
use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PatreonPollVoteConfig;
use App\Entity\PollVote;
use App\Entity\SubscribestarPollVoteConfig;
use App\Entity\SubscribestarSubscription;
use App\Entity\SubscribestarTier;
use App\Entity\SubscribestarUser;
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
        yield MenuItem::section('General');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::section('Patreon');
        yield MenuItem::linkToCrud('Campaigns', 'fa fa-list', PatreonCampaign::class);
        yield MenuItem::linkToCrud('Campaign Members', 'fa fa-users', PatreonCampaignMember::class);
        yield MenuItem::linkToCrud('Campaign Tiers', 'fa fa-rank-star', PatreonCampaignTier::class);
        yield MenuItem::linkToCrud('Member Entitlements', 'fa fa-lock-open', MemberEntitledTier::class);
        yield MenuItem::linkToCrud('Webhooks', 'fa fa-blog', PatreonCampaignWebhook::class);
        yield MenuItem::linkToCrud('Patreon Users', 'fa fa-user', PatreonUser::class);
        yield MenuItem::section('Subscribestar');
        yield MenuItem::linkToCrud('Subscribestar Tiers', 'fa fa-rank-star', SubscribestarTier::class);
        yield MenuItem::linkToCrud('Subscribestar Subscription', 'fa fa-rank-star', SubscribestarSubscription::class);
        yield MenuItem::linkToCrud('Subscribestar Vote Config', 'fa fa-flask', SubscribestarPollVoteConfig::class);
        yield MenuItem::linkToCrud('Subscribestar Users', 'fa fa-user', SubscribestarUser::class);
        yield MenuItem::section('Polls');
        yield MenuItem::linkToCrud('Polls', 'fa fa-square-poll-vertical', Poll::class);
        yield MenuItem::linkToCrud('Option', 'fa fa-filter', PollOption::class);
        yield MenuItem::linkToCrud('Votes', 'fa fa-check-to-slot', PollVote::class);
        yield MenuItem::linkToCrud('Patreon Vote Config', 'fa fa-flask', PatreonPollVoteConfig::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
