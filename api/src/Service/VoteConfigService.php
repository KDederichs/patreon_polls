<?php

namespace App\Service;

use App\Entity\AbstractVoteConfig;
use App\Entity\Poll;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollConfigRepository;
use App\Repository\PatreonUserRepository;

final readonly class VoteConfigService
{
    public function __construct(
        private PatreonUserRepository           $patreonUserRepository,
        private PatreonCampaignMemberRepository $patreonCampaignMemberRepository,
        private PatreonPollConfigRepository     $patreonPollConfigRepository,
    )
    {

    }

    public function getConfigForUser(Poll $poll, User $user): ?AbstractVoteConfig
    {
        return $this->getPatreonConfigForUser($poll, $user);
    }

    public function getPatreonConfigForUser(Poll $poll, User $user): ?AbstractVoteConfig
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
