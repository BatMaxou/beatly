<?php

namespace App\Controller;

use App\Repository\MusicFileRepository;
use App\Service\Mp3Streamer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsController]
class MusicFileController
{
    public function __construct(
        private readonly MusicFileRepository $musicFileRepository,
        private readonly StorageInterface $storage,
        private readonly Mp3Streamer $mp3Streamer,
    ) {
    }

    public function __invoke(int $id, Request $request): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return new Response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Range, Content-Type',
                'Access-Control-Max-Age' => '3600'
            ]);
        }

        $musicFile = $this->musicFileRepository->find($id);
        $path = $this->storage->resolvePath($musicFile, 'file');
        if (null === $path) {
            throw new NotFoundHttpException();
        }

        return $this->mp3Streamer->streamMp3File($path, $request);
    }
}

