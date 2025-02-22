<?php

namespace App\Controller;

use App\Entity\MediaObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

class MediaObjectController extends AbstractController
{
    #[Route('/api/media-object/{mediaObject}', name: 'get_media_object', methods: ["GET"])]
    public function getMediaObject(StorageInterface $storage, MediaObject $mediaObject, Request $request): Response
    {
        if (!$stream = $storage->resolveStream($mediaObject, 'file')) {
            throw $this->createNotFoundException();
        }
        $streamResponse = new StreamedResponse(static function () use ($stream) {
            \stream_copy_to_stream($stream, \fopen('php://output', 'wb'));
        });
        $mime = $mediaObject->getMimeType() ?? 'image/png';
        $disposition = $streamResponse->headers->makeDisposition(
            $request->get('download') ? ResponseHeaderBag::DISPOSITION_ATTACHMENT : ResponseHeaderBag::DISPOSITION_INLINE,
            sprintf("%s.%s", $mediaObject->getOriginalName() ?? $mediaObject->getFileName(), explode('/',$mime)[1])
        );
        $streamResponse->headers->set('Content-Disposition', $disposition);
        $streamResponse->headers->set('Content-Type', $mime);
        $streamResponse->headers->set('Cache-Control', 'public, max-age=604800');

        return $streamResponse;
    }
}
