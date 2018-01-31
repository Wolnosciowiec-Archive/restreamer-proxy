<?php declare(strict_types = 1);

namespace App\Controller;

use App\Exception\NoAvailableHandlerFoundError;
use App\Exception\ResourceNotFoundException;
use App\ResourceHandler\ResourceHandlerInterface;
use League\Uri\Http;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileDownloadController extends Controller
{
    /**
     * @var ResourceHandlerInterface $handler
     */
    private $handler;

    public function __construct(ResourceHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function downloadAction(Request $request): Response
    {
        try {
            return $this->handler->processRequestedUrl($request, Http::createFromString($request->get('url')));
            
        } catch (NoAvailableHandlerFoundError | ResourceNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }
}
