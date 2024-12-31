<?php

namespace App\Tests\Api\Poll;

use App\Factory\PollFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class ListPollTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $poll = PollFactory::createOne();
        $this
            ->browser()
            ->get('/api/polls')
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testGetPoll(): void
    {
        $poll = PollFactory::createOne();
        $user = UserFactory::createOne();

        $this
            ->browser()
            ->actingAs($user)
            ->get('/api/polls')
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('length(member)', 1)
            ->assertJsonMatches('totalItems', 1)
        ;
    }
}
