<?php

namespace App\Tests\Api\PollVote;

use App\Factory\PollFactory;
use App\Factory\PollOptionFactory;
use App\Factory\PollVoteFactory;
use App\Factory\SubscribestarPollVoteConfigFactory;
use App\Factory\SubscribestarSubscriptionFactory;
use App\Factory\SubscribestarTierFactory;
use App\Factory\SubscribestarUserFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Carbon\CarbonImmutable;

class CreatePollVoteTest extends ApiTestCase
{

    public function testItRequiresAuthentication(): void
    {
        $pollOption = PollOptionFactory::createOne();
        $this
            ->browser()
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122()
                ]
            ])
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testItCanNotVoteAfterPollHasEnded(): void
    {

        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, endsAt: CarbonImmutable::parse('yesterday'));
        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(422)
            ->assertJson()
            ->assertJsonMatches('detail', 'The poll has ended.');
    }

    public function testItCanNotAddVotesBeyondLimit(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, limitedVotes: true, numberOfVotes: 1);
        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);
        PollVoteFactory::createOne([
            'votedBy' => $user,
            'poll' => $poll,
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(422)
            ->assertJson()
            ->assertJsonMatches('detail', 'You can only vote 1 times.');
    }

    public function testCanNotVoteIfNotSubscribed(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user);
        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user2)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(403)
            ->assertJson()
            ->assertJsonMatches('detail', 'Access Denied.');
    }

    public function testCreatorCanVote(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2);
        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user2)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(201)
            ->assertJson()
            ->assertJsonMatches('votePower', 1)
            ->assertJsonMatches('pollOption', '/api/poll_options/'.$pollOption->getId()->toRfc4122())
        ;
    }

    public function testSubscriberCanVote(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, votingPower: 5);
        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(201)
            ->assertJson()
            ->assertJsonMatches('votePower', 5)
            ->assertJsonMatches('pollOption', '/api/poll_options/'.$pollOption->getId()->toRfc4122())
        ;
    }

    public function testSubscriberCanVoteSubscribestar(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = PollFactory::createOne([
            'createdBy' => $user,
            'endsAt' => null,
        ]);

        $subcribeStarUserCreator = SubscribestarUserFactory::createOne([
            'resourceId' => '1',
            'user' => $user2,
            'creator' => true
        ]);

        $subscribestarUser = SubscribestarUserFactory::createOne([
            'resourceId' => '2',
            'user' => $user,
        ]);

        $subscribeStarTier = SubscribestarTierFactory::createOne([
            'subscribestarUser' => $subcribeStarUserCreator
        ]);

        SubscribestarSubscriptionFactory::createOne([
            'active' => true,
            'contentProviderId' => '1',
            'subscribestarId' => '1',
            'tierId' => $subscribeStarTier->getSubscribestarTierId(),
            'subscribestarTier' => $subscribeStarTier,
            'subscribedTo' => $subcribeStarUserCreator,
            'subscribestarUser' => $subscribestarUser
        ]);

        SubscribestarPollVoteConfigFactory::createOne([
            'limitedVotes' => false,
            'poll' => $poll,
            'campaignTier' => $subscribeStarTier,
            'votingPower' => 5
        ]);


        $pollOption = PollOptionFactory::createOne([
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_votes', [
                'json' => [
                    'pollOption' => '/api/poll_options/'.$pollOption->getId()->toRfc4122(),
                    'poll' => '/api/polls/'.$poll->getId()
                ]
            ])
            ->assertStatus(201)
            ->assertJson()
            ->assertJsonMatches('votePower', 5)
            ->assertJsonMatches('pollOption', '/api/poll_options/'.$pollOption->getId()->toRfc4122())
        ;
    }
}
