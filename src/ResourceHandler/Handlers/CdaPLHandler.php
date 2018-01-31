<?php declare(strict_types = 1);

namespace App\ResourceHandler\Handlers;

use App\Exception\ResourceNotFoundException;
use App\ResourceHandler\ResourceHandlerInterface;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};

class CdaPLHandler implements ResourceHandlerInterface
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @inheritdoc
     */
    public function processRequestedUrl(Request $request, Http $url): Response
    {
        $response = $this->client->get((string) $url);
        $fileUrl = $this->scrape((string) $response->getBody());

        return new RedirectResponse($fileUrl, 307);
    }

    /**
     * @inheritdoc
     */
    public function isHandlingUrl(Http $url): bool
    {
        return preg_match('/cda.pl\/video\/([A-Za-z0-9]+)/i', $url)
            || preg_match('/ebd.cda.pl\/([0-9x]+)\/([A-Za-z0-9]+)/i', $url);
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
        $dom->loadHTML($body);

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
