<?php

namespace App\Tests\Api\Poll;

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
            ->assertJsonMatches('allowPictures', $poll->isAllowPictures());
    }

    public function testGetPoll(): void
    {
        $poll = PollFactory::createOne();
        $user = UserFactory::createOne();

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
        ;
    }
}
