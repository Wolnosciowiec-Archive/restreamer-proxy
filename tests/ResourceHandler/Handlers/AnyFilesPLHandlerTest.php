<?php declare(strict_types = 1);

namespace Tests\App\ResourceHandler;

use App\ResourceHandler\Handlers\AnyFilesPLHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpFoundation\Request;
use Tests\App\TestCase;

class AnyFilesPLHandlerTest extends TestCase
{
    /**
     * @group functional
     * @group integration
     *
     * @throws \Exception
     */
    public function testScrapesUrlProperly()
    {
        $handler = new AnyFilesPLHandler();
        $response = $handler->processRequestedUrl(new Request(), new Uri($this->findExampleVideoPageUrl()));
        $fileUrl  = $response->headers->get('location');

        $this->assertContains('.mp4', $fileUrl);
        $this->assertContains('anyfiles.pl/', $fileUrl);
    }

    /**
     * @group functional
     * @group integration
     */
    public function testHandlesUrl()
    {
        $handler = new AnyFilesPLHandler();

        $this->assertTrue($handler->isHandlingUrl(new Uri('https://anyfiles.pl/a/b/video/196566')));
        $this->assertFalse($handler->isHandlingUrl(new Uri('https://cda.pl/xxx/yyy')));
    }

    private function findExampleVideoPageUrl(): string
    {
        $client = new Client();
        $response = $client->get('https://anyfiles.pl/pageloading/tab-videos-loader.jsp?op=0');

        $body = $response->getBody()->getContents();
        preg_match_all('/video\/([0-9]+)/i', $body, $matches);

        if (!isset($matches[0][0])) {
            throw new \Exception('Cannot find a link to example video page');
        }

        return 'https://anyfiles.pl/a/b/video/' . $matches[0][0];
    }
}