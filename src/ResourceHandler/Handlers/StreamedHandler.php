<?php declare(strict_types = 1);

namespace App\ResourceHandler\Handlers;

use GuzzleHttp\Client;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class StreamedHandler
{
    const READ_BUFFER = 1024 * 1024 * 50;

    protected function restream(Request $userRequest, Http $fileUrl): StreamedResponse
    {
        $fileRequest = $this->getHttpClient()->get($fileUrl, [
            'stream' => true,
            'headers' =>
                array_merge(
                    iterator_to_array($userRequest->headers->getIterator(), true),
                    [
                        'Referer' => $userRequest->getUri()
                    ]
                )
            ]);

        $body = $fileRequest->getBody();

        $response = new StreamedResponse(function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(self::READ_BUFFER);
                ob_flush();
                usleep((int) (0.05 * 1000000));
            }
        }, $fileRequest->getStatusCode());

        return $response;
    }

    abstract protected function getHttpClient(): Client;
}
