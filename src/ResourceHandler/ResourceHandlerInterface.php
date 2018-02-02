<?php declare(strict_types = 1);

namespace App\ResourceHandler;

use App\Exception\{NoAvailableHandlerFoundError, ResourceNotFoundException};
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\{Request, Response};

/**
 * Takes a request to a resource, processes and returns
 * as a stream or redirection (depends on what is possible)
 */
interface ResourceHandlerInterface
{
    /**
     * @param UriInterface $url
     * @return bool
     */
    public function isHandlingUrl(UriInterface $url): bool;

    /**
     * @param Request $request
     * @param UriInterface $url
     *
     * @return Response
     *
     * @throws ResourceNotFoundException
     * @throws NoAvailableHandlerFoundError
     */
    public function processRequestedUrl(Request $request, UriInterface $url): Response;
}
