<?php declare(strict_types = 1);

namespace App\Controller;

use App\ActionHandler\DeleteAction;
use App\ActionHandler\ServeLibraryElementAction;
use App\Exception\NoAvailableHandlerFoundError;
use App\Exception\ResourceNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrowseLibraryController extends Controller
{
    /**
     * @var ServeLibraryElementAction $serve
     */
    protected $serve;

    /**
     * @var DeleteAction $delete
     */
    protected $delete;

    public function __construct(
        ServeLibraryElementAction $serve,
        DeleteAction $delete
    ) {
        $this->serve = $serve;
        $this->delete = $delete;
    }

    /**
     * @param Request $request
     * @param string $libraryFileId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function streamFileByIdAction(Request $request, string $libraryFileId): Response
    {
        try {
            return $this->serve->serveAction($request, $libraryFileId);

        } catch (NoAvailableHandlerFoundError | ResourceNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param Request $request
     * @param string $libraryId
     * @param string $url
     *
     * @return JsonResponse
     */
    public function deleteByIdAction(Request $request, string $libraryId, string $url)
    {
        return new JsonResponse(['object' => $this->delete->deleteAction($libraryId, base64_decode($url))]);
    }

    public function addFileAction(Request $request, string $libraryId, string $url)
    {

    }
}
