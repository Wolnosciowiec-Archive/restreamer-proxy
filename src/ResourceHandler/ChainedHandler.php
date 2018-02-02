<?php declare(strict_types = 1);

namespace App\ResourceHandler;

use App\Exception\NoAvailableHandlerFoundError;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\{Request, Response};

class ChainedHandler implements ResourceHandlerInterface
{
    /**
     * @var ResourceHandlerInterface[] $handlers
     */
    private $handlers;

    /**
     * @param ResourceHandlerInterface[] $handlers
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @inheritdoc
     */
    public function isHandlingUrl(UriInterface $url): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->isHandlingUrl($url)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @inheritdoc
     */
    public function processRequestedUrl(Request $request, UriInterface $url): Response
    {
        foreach ($this->handlers as $handler) {
            if ($handler->isHandlingUrl($url)) {
                return $handler->processRequestedUrl($request, $url);
            }
        }

        throw NoAvailableHandlerFoundError::create();
    }
}
