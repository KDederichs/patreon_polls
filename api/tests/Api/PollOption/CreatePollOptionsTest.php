<?php

namespace App\Tests\Api\PollOption;


use App\Factory\MediaObjectFactory;
use App\Factory\PatreonUserFactory;
use App\Factory\PollOptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreatePollOptionsTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $this
            ->browser()
            ->post('/api/poll_options')
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testCanNotCreateImageOptionWhenDisabled(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, addOptions: true, maxOptionAdd: 1);
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');
        PatreonUserFactory::repository()->assert()->notEmpty();

        $mediaObject = MediaObjectFactory::createOne([
            'file' => $file
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                    'image' => '/api/media_objects/'.$mediaObject->getId()->toRfc4122(),
                ]
            ])
            ->assertStatus(422)
            ->assertJson()
            ->assertJsonMatches('detail', 'Adding pictures to poll options is not allowed.');
    }

    public function testCanNotCreateOptionWhenOverLimit(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, addOptions: true, maxOptionAdd: 1);
        PollOptionFactory::createOne([
            'poll' => $poll,
            'createdBy' => $user
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                ]
            ])
            ->assertStatus(422)
            ->assertJson()
            ->assertJsonMatches('detail', 'You can not add more than 1 options.');
    }

    public function testCanNotCreateOptionWhenOptionCreationDisabled(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2);
        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                ]
            ])
            ->assertStatus(422)
            ->assertJson()
            ->assertJsonMatches('detail', 'Adding options for this poll is not enabled.');
    }

    public function testCanNotCreateOptionWhenNotSubscribed(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user, addOptions: true, maxOptionAdd: 1);
        PollOptionFactory::createOne([
            'poll' => $poll,
            'createdBy' => $user
        ]);

        $this
            ->browser()
            ->actingAs($user2)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                ]
            ])
            ->assertStatus(403)
            ->assertJson()
            ->assertJsonMatches('detail', 'Access Denied.');
    }

    public function testCanCreateOptionWhenCreator(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2);
        $this
            ->browser()
            ->actingAs($user2)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                ]
            ])
            ->assertStatus(201)
            ->assertJson()
            ->assertJsonMatches('optionName', 'Testi Option')
            ->assertJsonMatches('numberOfVotes', 1);
    }

    public function testCanCreateOption(): void
    {
        $user = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        $poll = $this->setUpPoll($user, $user2, allowImages: true, addOptions: true, maxOptionAdd: 100);
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');

        $mediaObject = MediaObjectFactory::createOne([
            'file' => $file
        ]);
        $this
            ->browser()
            ->actingAs($user)
            ->post('/api/poll_options', [
                'json' => [
                    'poll' => '/api/polls/'.$poll->getId()->toRfc4122(),
                    'optionName' => 'Testi Option',
                    'image' => '/api/media_objects/'.$mediaObject->getId()->toRfc4122(),
                ]
            ])
            ->assertStatus(201)
            ->assertJson()
            ->assertJsonMatches('optionName', 'Testi Option')
            ->assertJsonMatches('numberOfVotes', 1)
            ->assertJsonMatches('imageUri', 'http://localhost/api/media-object/'.$mediaObject->getId()->toRfc4122());
    }
}
