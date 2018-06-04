<?php declare(strict_types = 1);

namespace App\ResourceHandler\Handlers;

use App\Exception\ResourceNotFoundException;
use App\ResourceHandler\ResourceHandlerInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};

class AnyFilesPLHandler implements ResourceHandlerInterface
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    protected function getHttpClient(): Client
    {
        return $this->client;
    }

    public function getSupportedHosts(): array
    {
        return ['anyfiles.pl'];
    }

    /**
     * @inheritdoc
     */
    public function processRequestedUrl(Request $request, UriInterface $url): Response
    {
        $response = $this->client->get((string) $url, [
            'headers' => [
                'User-Agent' => $request->headers->get('User-Agent') ?? 'Mozilla/5.0 (Android 7.1; Mobile; rv:51.0) Gecko/41.0 Firefox/51.0'
            ]
        ]);

        $fileUrl = $this->scrape((string) $response->getBody());
        return new RedirectResponse($fileUrl, RedirectResponse::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @inheritdoc
     */
    public function isHandlingUrl(UriInterface $url): bool
    {
        return (bool) preg_match('/anyfiles.pl\/(.*)\/([0-9]+)/i', (string) $url);
    }

    /**
     * Extract video URL from the page content
     *
     * @param string $body
     *
     * @return string URL to the file download
     * @throws ResourceNotFoundException
     */
    private function scrape(string $body): string
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($body, LIBXML_NOERROR);

        /**
         * @var \DOMElement[] $nodes
         */
        $xPath = new \DOMXPath($dom);
        $nodes = $xPath->query('//meta[starts-with(@property, \'og:video:url\')]');

        if (count($nodes) === 0) {
            throw ResourceNotFoundException::create();
        }

        $content = explode('?file=', $nodes[0]->getAttribute('content'));
        
        if (count($content) < 2) {
            throw ResourceNotFoundException::create();
        }

        return $content[1];
    }
}
