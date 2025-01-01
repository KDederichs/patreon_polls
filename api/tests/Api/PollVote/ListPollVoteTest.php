<?php

namespace App\Tests\Api\PollVote;

use App\Factory\PollFactory;
use App\Factory\PollVoteFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class ListPollVoteTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $pollVote = PollVoteFactory::createOne();
        $poll = PollFactory::createOne();
        $this
            ->browser()
            ->get(sprintf('/api/polls/%s/my-votes', $poll->getId()))
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testItListsMyVotes(): void
    {
        $user = UserFactory::createOne();
        $poll = PollFactory::createOne();

        PollVoteFactory::createMany(5,[
            'votedBy' => $user,
            'poll' => $poll
        ]);
        PollVoteFactory::createMany(3,[
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->get(sprintf('/api/polls/%s/my-votes', $poll->getId()))
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('length(member)', 5)
            ->assertJsonMatches('totalItems', 5)
        ;
    }
}
