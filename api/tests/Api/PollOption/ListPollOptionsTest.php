<?php

namespace App\Tests\Api\PollOption;

use App\Factory\PollFactory;
use App\Factory\PollOptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class ListPollOptionsTest extends ApiTestCase
{
    public function testItDoesNotListAllExistingOptions()
    {
        $user = UserFactory::createOne();
        $this
            ->browser()
            ->actingAs($user)
            ->get('/api/poll_options')
            ->assertStatus(404)
            ->assertJson()
            ->assertJsonMatches('detail', '');
    }

    public function testItListsOptionsForPoll()
    {
        $poll = PollFactory::createOne();
        PollOptionFactory::createMany(3);
        PollOptionFactory::createMany(5, [
            'poll' => $poll
        ]);

        $this
            ->browser()
            ->get(sprintf('/api/polls/%s/options', $poll->getId()->toRfc4122()))
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('length(member)', 5)
            ->assertJsonMatches('totalItems', 5);
    }
}
