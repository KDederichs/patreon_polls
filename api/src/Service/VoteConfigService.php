<?php

namespace App\Service;

use App\Entity\AbstractVoteConfig;
use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollConfigRepository;
use App\Repository\PatreonUserRepository;
use App\Repository\SubscribestarPollConfigRepository;
use App\Repository\SubscribestarSubscriptionRepository;
use App\Repository\SubscribestarUserRepository;

final readonly class VoteConfigService
{
    public function __construct(
        private PatreonUserRepository           $patreonUserRepository,
        private PatreonCampaignMemberRepository $patreonCampaignMemberRepository,
        private PatreonPollConfigRepository     $patreonPollConfigRepository,
        private SubscribestarUserRepository $subscribestarUserRepository,
        private SubscribestarPollConfigRepository $subscribestarPollConfigRepository,
        private SubscribestarSubscriptionRepository $subscribestarSubscriptionRepository
    )
    {

    }

    public function getConfigForUser(Poll $poll, User $user): ?AbstractVoteConfig
    {
        $patreonConfig = $this->getPatreonConfigForUser($poll, $user);
        $subscribestarConfig = $this->getSubscribestarConfigForUser($poll, $user);

        $patreonVotePower = $patreonConfig?->getVotingPower() ?? -1;
        $subscribestarVotePower = $subscribestarConfig?->getVotingPower() ?? -1;

        if (!$patreonConfig && !$subscribestarConfig) {
            return null;
        }

        if ($patreonVotePower > $subscribestarVotePower) {
            return $patreonConfig;
        }

        return $subscribestarConfig;
    }

    private function getSubscribestarConfigForUser(Poll $poll, User $user):? AbstractVoteConfig
    {
        $subscribeStarUser = $this->subscribestarUserRepository->findByUser($user);
        if (!$subscribeStarUser) {
            return null;
        }

        $subscirbestarConfigs = $this->subscribestarPollConfigRepository->findByPoll($poll);
        if (empty($subscirbestarConfigs)) {
            return null;
        }
        $creator = $subscirbestarConfigs[0]->getCampaignTier()->getSubscribestarUser();
        $subscriptions = $this->subscribestarSubscriptionRepository->findActiveBySubscribeStarUser($subscribeStarUser, $creator);
        $subscription = null;
        foreach ($subscriptions as $sub) {
            if (!$subscription) {
                $subscription = $sub;
            }
            if ($sub->getSubscribestarTier()->getAmountInCents() > $subscription->getSubscribestarTier()->getAmountInCents()) {
                $subscription = $sub;
            }
        }

        if (!$subscription) {
            return null;
        }

        foreach ($subscirbestarConfigs as $config) {
            if ($config->getCampaignTier()->getId()->equals($subscription->getSubscribestarTier()->getId())) {
                return $config;
            }
        }
        return null;
    }

    private function getPatreonConfigForUser(Poll $poll, User $user): ?AbstractVoteConfig
    {
        $patreonUser = $this->patreonUserRepository->findByUser($user);

        if (!$patreonUser) {
            return null;
        }

        $patreonConfigs = $this->patreonPollConfigRepository->findByPoll($poll);
        if (empty($patreonConfigs)) {
            return null;
        }

        $campaign = $patreonConfigs[0]->getCampaignTier()->getCampaign();
        $campaignMembership = $this->patreonCampaignMemberRepository->findByCampaignAndPatreonUser($campaign, $patreonUser);
        if (!$campaignMembership) {
            return null;
        }

        $highestPatreonTier = $campaignMembership->getHighestEntitledTier();

        if (!$highestPatreonTier) {
            return null;
        }

        foreach ($patreonConfigs as $patreonConfig) {
            if ($patreonConfig->getCampaignTier()->getId()->equals($highestPatreonTier->getTier()->getId())) {
                return $patreonConfig;
            }
        }

        return null;
    }
}
