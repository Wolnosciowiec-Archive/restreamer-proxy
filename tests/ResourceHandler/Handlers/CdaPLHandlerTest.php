<?php declare(strict_types = 1);

namespace Tests\App\ResourceHandler;

use App\ResourceHandler\Handlers\CdaPLHandler;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpFoundation\Request;
use Tests\App\TestCase;

class CdaPLHandlerTest extends TestCase
{
    /**
     * @group functional
     * @group integration
     *
     * @throws \Exception
     */
    public function testScrapesUrlProperly()
    {
        $handler = new CdaPLHandler();
        $response = $handler->processRequestedUrl(new Request(), new Uri('https://www.cda.pl/video/94257305'));
        $redirectUrl = $response->headers->get('location');

        $this->assertContains('.mp4', $redirectUrl);
        $this->assertContains('.cda.pl', $redirectUrl);
    }

    /**
     * @group functional
     * @group integration
     */
    public function testHandlesUrl()
    {
        $handler = new CdaPLHandler();

        $this->assertTrue($handler->isHandlingUrl(new Uri('https://www.cda.pl/video/149120de')));
        $this->assertFalse($handler->isHandlingUrl(new Uri('https://cda.pl/')));
    }
}