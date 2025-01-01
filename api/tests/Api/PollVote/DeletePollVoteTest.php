<?php

namespace App\Tests\Api\PollVote;

use App\Factory\PollVoteFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;

class DeletePollVoteTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $pollVote = PollVoteFactory::createOne();
        $this
            ->browser()
            ->delete('/api/poll_votes/'.$pollVote->getId()->toRfc4122())
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testCanNotDeleteForeignVotes(): void
    {
        $user = UserFactory::createOne();
        $pollVote = PollVoteFactory::createOne();
        $this
            ->browser()
            ->actingAs($user)
            ->delete('/api/poll_votes/'.$pollVote->getId()->toRfc4122())
            ->assertStatus(404)
            ->assertJson()
            ->assertJsonMatches('detail', 'Not Found');
    }

    public function testItDeletesVote(): void
    {
        $user = UserFactory::createOne();
        $pollVote = PollVoteFactory::createOne([
            'votedBy' => $user
        ]);
        $this
            ->browser()
            ->actingAs($user)
            ->delete('/api/poll_votes/'.$pollVote->getId()->toRfc4122())
            ->assertStatus(204);
    }
}
