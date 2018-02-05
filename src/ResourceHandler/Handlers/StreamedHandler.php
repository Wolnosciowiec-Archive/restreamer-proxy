<?php declare(strict_types = 1);

namespace App\ResourceHandler\Handlers;

use GuzzleHttp\Client;
use League\Uri\Http;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use Symfony\Component\HttpFoundation\{Request, StreamedResponse};

/**
 * Transparent proxying of streamed media like eg. videos
 */
abstract class StreamedHandler
{
    const READ_BUFFER = 1024 * 1024 * 1000;
    const DEFAULT_CONNECT_TIMEOUT = 120;

    /**
     * Download an external file and serve at the same time
     * (be a transparent proxy that outputs the body and the headers directly)
     *
     * Should be used instead of redirection when an external streaming service
     * has blocked the hotlinking. In this case it's required to re-stream the file
     * to the user instead of just pointing the file to the user.
     *
     * @param Request $userRequest
     * @param Http $fileUrl
     *
     * @return StreamedResponse
     */
    protected function restream(Request $userRequest, Http $fileUrl): StreamedResponse
    {
        $fileRequest = $this->getHttpClient()->get($fileUrl, [
            'stream' => true,
            'connect_timeout' => $this->getConnectTimeout(),
            'headers' =>
                array_merge(
                    iterator_to_array($userRequest->headers->getIterator(), true),
                    [
                        'Referer' => $userRequest->getUri()
                    ],
                    $this->getStreamRequestHeaders()
                )
            ]);

        $body = $fileRequest->getBody();
        return $this->createRestreamBufferedResponse($body, $fileRequest);
    }

    abstract protected function getHttpClient(): Client;

    /**
     * @param StreamInterface $body
     * @param ResponseInterface $fileRequest
     *
     * @return StreamedResponse
     */
    protected function createRestreamBufferedResponse(StreamInterface $body, ResponseInterface $fileRequest)
    {
        $response = new StreamedResponse(
            function () use ($body) {
                $maxExecTime = ini_get('max_execution_time');
                set_time_limit(0);

                while (!$body->eof()) {
                    echo $body->read(self::READ_BUFFER);
                    ob_flush();
                    usleep((int) (0.0005 * 1000000));
                }

                set_time_limit($maxExecTime);
            },
            $fileRequest->getStatusCode(),
            $fileRequest->getHeaders()
        );

        return $response;
    }

    /**
     * @override
     * @return int
     */
    protected function getConnectTimeout(): int
    {
        return self::DEFAULT_CONNECT_TIMEOUT;
    }

    /**
     * @override
     * @return array
     */
    protected function getStreamRequestHeaders(): array
    {
        return [];
    }
}
