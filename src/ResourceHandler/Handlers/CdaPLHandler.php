<?php declare(strict_types = 1);

namespace App\ResourceHandler\Handlers;

use App\Exception\ResourceNotFoundException;
use App\ResourceHandler\ResourceHandlerInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};

class CdaPLHandler extends StreamedHandler implements ResourceHandlerInterface
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

    /**
     * @inheritdoc
     */
    public function getSupportedHosts(): array
    {
        return ['cda.pl'];
    }

    /**
     * @inheritdoc
     */
    public function processRequestedUrl(Request $request, UriInterface $url): Response
    {
        $response = $this->client->get($this->convertToEmbeddedVersion((string) $url), [
            'headers' => [
                'User-Agent' => $request->headers->get('User-Agent') ?? 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
            ]
        ]);

        $fileUrl = $this->scrape((string) $response->getBody());
        return new RedirectResponse($fileUrl, 307);
    }

    /**
     * @inheritdoc
     */
    public function isHandlingUrl(UriInterface $url): bool
    {
        return preg_match('/cda.pl\/video\/([A-Za-z0-9]+)/i', (string) $url)
            || preg_match('/ebd.cda.pl\/([0-9x]+)\/([A-Za-z0-9]+)/i', (string) $url);
    }

    private function convertToEmbeddedVersion(string $url): string
    {
        if (preg_match('/cda.pl\/video\/([A-Za-z0-9]+)/i', $url, $matches)) {
            return 'http://ebd.cda.pl/300x150/' . $matches[1];
        }

        return $url;
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
        $nodes = $xPath->query('//div[@player_data]');

        if (count($nodes) === 0) {
            throw ResourceNotFoundException::create();
        }

        $json = $nodes[0]->getAttribute('player_data');
        $data = \GuzzleHttp\json_decode($json, true);
        
        if (!isset($data['video']['file'])) {
            throw ResourceNotFoundException::create();
        }

        return $data['video']['file'];
    }
}
