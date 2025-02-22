<?php

namespace App\Tests\Api\Poll;

use App\Factory\MemberEntitledTierFactory;
use App\Factory\PatreonCampaignFactory;
use App\Factory\PatreonCampaignMemberFactory;
use App\Factory\PatreonCampaignTierFactory;
use App\Factory\PatreonPollVoteConfigFactory;
use App\Factory\PatreonUserFactory;
use App\Factory\PollFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class GetPollTest extends ApiTestCase
{
    public function testItAllowsAnnonAccess(): void
    {
        $poll = PollFactory::createOne();

        $this
            ->browser()
            ->get('/api/polls/'.$poll->getId()->toRfc4122())
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('id', $poll->getId()->toRfc4122())
            ->assertJsonMatches('createdAt', $poll->getCreatedAt()->toIso8601String())
            ->assertJsonMatches('pollName', $poll->getPollName())
            ->assertJsonMatches('config', null)
            ->assertJsonMatches('allowPictures', $poll->isAllowPictures());
    }

    public function testGetPoll(): void
    {
        $poll = PollFactory::createOne();
        $user = UserFactory::createOne();
        $patreonUser = PatreonUserFactory::createOne([
            'user' => $user,
        ]);
        $campaign = PatreonCampaignFactory::createOne([
            'campaignOwner' => $user,
        ]);
        $campaignTier = PatreonCampaignTierFactory::createOne([
            'campaign' => $campaign
        ]);
        $voteConfig = PatreonPollVoteConfigFactory::createOne([
            'poll' => $poll,
            'campaignTier' => $campaignTier,
        ]);
        $membership = PatreonCampaignMemberFactory::createOne([
            'patreonUser' => $patreonUser,
            'campaign' => $campaign,
        ]);
        MemberEntitledTierFactory::createOne([
            'campaignMember' => $membership,
            'tier' => $campaignTier
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->get('/api/polls/'.$poll->getId()->toRfc4122())
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('id', $poll->getId()->toRfc4122())
            ->assertJsonMatches('createdAt', $poll->getCreatedAt()->toIso8601String())
            ->assertJsonMatches('pollName', $poll->getPollName())
            ->assertJsonMatches('allowPictures', $poll->isAllowPictures())
            ->assertJsonMatches('config.numberOfOptions', $voteConfig->getMaxOptionAdd())
            ->assertJsonMatches('config.numberOfVotes', $voteConfig->getNumberOfVotes())
            ->assertJsonMatches('config.votingPower', $voteConfig->getVotingPower())
            ->assertJsonMatches('config.canAddOptions', $voteConfig->isAddOptions())
            ->assertJsonMatches('config.hasLimitedVotes', $voteConfig->isLimitedVotes())
        ;
    }
}
