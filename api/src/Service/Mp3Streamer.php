<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Mp3Streamer
{
    private const CHUNK_SIZE = 8192;

    public function streamMp3File(string $filePath, Request $request): Response
    {
        if (!file_exists($filePath)) {
            return new Response('File not found', 404);
        }

        $fileSize = filesize($filePath);
        if (false === $fileSize) {
            return new Response('Could not determine file size', 500);
        }

        $start = 0;
        $end = $fileSize - 1;

        // Is range header supported
        $isRangeRequest = false;
        $rangeHeader = $request->headers->get('Range');
        if ($rangeHeader) {
            $isRangeRequest = true;

            if (preg_match('/bytes=(\d+)-(\d*)/', $rangeHeader, $matches)) {
                $start = intval($matches[1]);
                $end = '' !== $matches[2] ? intval($matches[2]) : $fileSize - 1;
            }
        }

        if ($start > $end || $start < 0 || $end >= $fileSize) {
            return new Response('Requested Range Not Satisfiable', 416, [
                'Content-Range' => sprintf('bytes */%d', $fileSize),
            ]);
        }

        $contentLength = $end - $start + 1;

        $response = new StreamedResponse(function () use ($filePath, $start, $end) {
            $this->outputFileRange($filePath, $start, $end);
        });

        $response->headers->set('Content-Type', 'audio/mpeg');
        $response->headers->set('Content-Length', (string) $contentLength);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Cache-Control', 'public, max-age=3600');

        if ($isRangeRequest) {
            $response->setStatusCode(206);
            $response->headers->set('Content-Range', sprintf('bytes %d-%d/%d', $start, $end, $fileSize));
        } else {
            $response->setStatusCode(200);
        }

        return $response;
    }

    private function outputFileRange(string $filePath, int $start, int $end): void
    {
        $file = fopen($filePath, 'rb');
        if (!$file) {
            return;
        }

        fseek($file, $start);
        $bytesRemaining = $end - $start + 1;

        while ($bytesRemaining > 0 && !feof($file)) {
            $chunkSize = min(self::CHUNK_SIZE, $bytesRemaining);
            $chunk = fread($file, $chunkSize);
            if (false === $chunk) {
                break;
            }

            echo $chunk;
            flush();

            $bytesRemaining -= strlen($chunk);

            // Stop if the connection is aborted
            if (connection_aborted()) {
                break;
            }
        }

        fclose($file);
    }
}
