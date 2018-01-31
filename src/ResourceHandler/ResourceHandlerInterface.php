<?php declare(strict_types = 1);

namespace App\ResourceHandler;

use App\Exception\NoAvailableHandlerFoundError;
use App\Exception\ResourceNotFoundException;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\{Request, Response};

/**
 * Takes a request to a resource, processes and returns
 * as a stream or redirection (depends on what is possible)
 */
interface ResourceHandlerInterface
{
    /**
     * @param Http $url
     * @return bool
     */
    public function isHandlingUrl(Http $url): bool;

    /**
     * @param Request $request
     * @param Http $url
     *
     * @return Response
     *
     * @throws ResourceNotFoundException
     * @throws NoAvailableHandlerFoundError
     */
    public function processRequestedUrl(Request $request, Http $url): Response;
}
