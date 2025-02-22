<?php

namespace App\Tests\Api\MediaObject;

use App\Factory\MediaObjectFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DeleteMediaObjectTest extends ApiTestCase
{
    public function testItRequiresAuthentication(): void
    {
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');

        $mediaObject = MediaObjectFactory::createOne([
            'file' => $file
        ]);

        $this
            ->browser()
            ->delete('/api/media_objects/'.$mediaObject->getId()->toRfc4122())
            ->assertStatus(401)
            ->assertJson()
            ->assertJsonMatches('detail', 'Full authentication is required to access this resource.');
    }

    public function testItCanNotDeleteForeignMediaObejcts(): void
    {
        $user = UserFactory::createOne();
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');
        $mediaObject = MediaObjectFactory::createOne([
            'file' => $file,
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->delete('/api/media_objects/'.$mediaObject->getId()->toRfc4122())
            ->assertStatus(403);
    }

    public function testItDeletedMediaObject(): void
    {
        $user = UserFactory::createOne();
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');
        $mediaObject = MediaObjectFactory::createOne([
            'file' => $file,
            'uploadedBy' => $user
        ]);

        $this
            ->browser()
            ->actingAs($user)
            ->delete('/api/media_objects/'.$mediaObject->getId()->toRfc4122())
            ->assertStatus(204);
        MediaObjectFactory::repository()->assert()->empty();
    }
}
