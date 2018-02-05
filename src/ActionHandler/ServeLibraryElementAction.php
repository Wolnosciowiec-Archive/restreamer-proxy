<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\Entity\LibraryElement;
use App\Exception\NoAvailableHandlerFoundError;
use App\Exception\ResourceNotFoundException;
use App\Repository\LibraryElementRepository;
use App\ResourceHandler\ResourceHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves files in order (if one source fails, then next)
 */
class ServeLibraryElementAction
{
    /**
     * @var LibraryElementRepository $repository
     */
    private $repository;

    /**
     * @var ResourceHandlerInterface $handler
     */
    private $handler;

    /**
     * @param LibraryElementRepository $repository
     * @param ResourceHandlerInterface $handler
     */
    public function __construct(LibraryElementRepository $repository, ResourceHandlerInterface $handler)
    {
        $this->repository = $repository;
        $this->handler    = $handler;
    }

    /**
     * Serve files by:
     * 1. Iterating the download urls of a library elements IN ORDER
     * 2. Asking a proper handler for each file
     * 3. If a handler fails, then next is called
     *
     * @param Request $request
     * @param string $libraryFileId
     *
     * @return Response
     * @throws ResourceNotFoundException
     * @throws \Throwable
     */
    public function serveAction(Request $request, string $libraryFileId): Response
    {
        $element = $this->repository->findById($libraryFileId);

        if (!$element instanceof LibraryElement) {
            throw ResourceNotFoundException::create();
        }

        $lastException = null;
        $response      = null;

        foreach ($element->getOrderedUrls() as $url) {
            try {
                $response = $this->handler->processRequestedUrl(
                    $request,
                    $url->getUrl()
                );
            } catch (\Throwable $lastException) {
                continue;
            }
            
            if ($response instanceof Response) {
                break;
            }
        }

        if ($lastException instanceof \Throwable && !$response instanceof Response) {
            throw $lastException;
        }
        
        // no file was found, eg. all files were removed from the library
        if (!$response instanceof Response) {
            throw new NotFoundHttpException();
        }

        return $response;
    }
}
