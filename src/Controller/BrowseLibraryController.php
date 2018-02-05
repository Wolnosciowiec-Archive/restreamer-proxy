<?php declare(strict_types = 1);

namespace App\Controller;

use App\ActionHandler\AddLinkAction;
use App\ActionHandler\DeleteLinkAction;
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
     * @var DeleteLinkAction $delete
     */
    protected $delete;

    /**
     * @var AddLinkAction $add
     */
    protected $add;

    public function __construct(
        ServeLibraryElementAction $serve,
        DeleteLinkAction $delete,
        AddLinkAction $add
    ) {
        $this->serve  = $serve;
        $this->delete = $delete;
        $this->add    = $add;
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
        return new JsonResponse($this->delete->deleteAction($libraryId, base64_decode($url)));
    }

    /**
     * @param Request $request
     * @param string $libraryId
     * @param string $url
     *
     * @return JsonResponse
     */
    public function addFileAction(Request $request, string $libraryId, string $url)
    {
        return new JsonResponse($this->add->createAction($libraryId, base64_decode($url)));
    }
}
