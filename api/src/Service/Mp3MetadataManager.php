<?php

namespace App\Service;

use wapmorgan\Mp3Info\Mp3Info;

/**
 * @template Mp3Metadata of array{
 *  duration: int,
 * }
 */
class Mp3MetadataManager
{
    /**
     * @return Mp3Metadata
     */
    public function getFor(string $filePath): array
    {
        $mp3Info = new Mp3Info($filePath, true);

        return [
            'duration' => (int) round($mp3Info->duration ?? 1) - 1,
        ];
    }
}
