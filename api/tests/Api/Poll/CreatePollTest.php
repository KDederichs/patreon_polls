<?php

namespace App\Tests\Api\Poll;

use App\Factory\PatreonCampaignFactory;
use App\Factory\PatreonCampaignTierFactory;
use App\Factory\PatreonPollVoteConfigFactory;
use App\Factory\PatreonUserFactory;
use App\Factory\PollFactory;
use App\Factory\SubscribestarPollVoteConfigFactory;
use App\Factory\SubscribestarTierFactory;
use App\Factory\SubscribestarUserFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class CreatePollTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $this
            ->browser()
            ->post('/api/polls')
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testOnlyCreatorShouldBeAbleToCreatePolls()
    {
        $user = UserFactory::createOne();

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/polls', [
                'json' => [
                    'pollName' => 'Testi Poll',
                    'endsAt' => '01.01.2027 00:00:00',
                    'allowPictures' => false,
                    'voteConfig' => [
                        'api/patreon_campaign_tiers/1' => [
                            'votingPower' => 1,
                            'hasLimitedVotes' => false,
                            'canAddOptions' => true,
                            'numberOfOptions' => 20
                        ],
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/ld+json'
                ]
            ])
            ->assertStatus(403)
            ->assertJson();
    }

    public function testCanCreatePoll(): void
    {
        $user = UserFactory::createOne([
            'patreonId' => '1',
            'subscribestarId' => '1'
        ]);

        $subcribeStarUser = SubscribestarUserFactory::createOne([
            'resourceId' => '1',
            'user' => $user,
            'creator' => true
        ]);

        PatreonUserFactory::createOne([
            'resourceId' => '1',
            'user' => $user,
            'creator' => true
        ]);

        $subscribeStarTier = SubscribestarTierFactory::createOne([
            'subscribestarUser' => $subcribeStarUser
        ]);

        $patreonCampaign = PatreonCampaignFactory::createOne([
            'campaignOwner' => $user
        ]);
        $patreonCampaignTier1 = PatreonCampaignTierFactory::createOne([
            'campaign' => $patreonCampaign
        ]);
        $patreonCampaignTier2 = PatreonCampaignTierFactory::createOne([
            'campaign' => $patreonCampaign
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/polls', [
                'json' => [
                    'pollName' => 'Testi Poll',
                    'endsAt' => '01.01.2027 00:00:00',
                    'allowPictures' => false,
                    'voteConfig' => [
                        'api/patreon_campaign_tiers/'.$patreonCampaignTier1->getId()->toRfc4122() => [
                            'votingPower' => 1,
                            'hasLimitedVotes' => false,
                            'canAddOptions' => true,
                            'numberOfOptions' => 20
                        ],
                        'api/patreon_campaign_tiers/'.$patreonCampaignTier2->getId()->toRfc4122() => [
                            'votingPower' => 10,
                            'hasLimitedVotes' => true,
                            'canAddOptions' => false,
                            'numberOfVotes' => 20
                        ],
                        'api/subscribestar_tiers/'.$subscribeStarTier->getId()->toRfc4122() => [
                            'votingPower' => 10,
                            'hasLimitedVotes' => true,
                            'canAddOptions' => false,
                            'numberOfVotes' => 20
                        ]
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/ld+json'
                ]
            ])
            ->assertStatus(201)
            ->assertJson();

        PollFactory::repository()->assert()->count(1);
        $poll = PollFactory::repository()->firstOrFail();
        self::assertNotNull($poll->getEndsAt());
        PatreonPollVoteConfigFactory::repository()->assert()->count(2);
        SubscribestarPollVoteConfigFactory::repository()->assert()->count(1);
    }

}
