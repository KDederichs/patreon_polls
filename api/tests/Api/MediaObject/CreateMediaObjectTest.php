<?php

namespace App\Tests\Api\MediaObject;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\MediaObjectFactory;
use App\Factory\UserFactory;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function PHPUnit\Framework\assertEquals;

class CreateMediaObjectTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testItRequiresAuthentication(): void
    {
        $client = self::createClient();
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');
        $response = $client->request('POST', 'api/media_objects', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $file,
                ],
            ],
        ]);
        self::assertEquals(401, $response->getStatusCode());
    }

    public function testMediaObjectUpload(): void
    {
        $user = UserFactory::createOne();
        $client = self::createClient();
        $client->disableReboot();
        $client->loginUser($user->_real());
        $file = new UploadedFile(__DIR__ . '/../../fixtures/test_image.png', 'test_image.png');
        $response = $client
            ->request('POST', 'api/media_objects', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $file,
                ],
            ],
        ]);

        self::assertEquals(201, $response->getStatusCode());
        MediaObjectFactory::repository()->assert()->count(1);
        $mediaObject = MediaObjectFactory::repository()->firstOrFail();
        $responsePayload = json_decode($response->getContent(), true);
        assertEquals($responsePayload['@id'], '/api/media_objects/'.$mediaObject->getId()->toRfc4122());
        assertEquals($responsePayload['contentUrl'], 'http://localhost/api/media-object/'.$mediaObject->getId()->toRfc4122());
        self::assertNotNull($mediaObject->getUploadedBy());
        self::assertTrue($mediaObject->getUploadedBy()->getId()->equals($user->getId()));

        $uploadedFileName = $mediaObject->getFileName();
        /** @var FilesystemOperator $filesystem */
        $filesystem = self::getContainer()->get('flysystem.adapter.default.storage');
        self::assertTrue($filesystem->fileExists($uploadedFileName));
        self::assertStringEqualsFile($file->getRealPath(), $filesystem->read($uploadedFileName));
    }
}
