<?php declare(strict_types = 1);

namespace App\Controller;

use App\ActionHandler\AddLinkAction;
use App\ActionHandler\BrowseAction;
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
    private $serve;

    /**
     * @var DeleteLinkAction $delete
     */
    private $delete;

    /**
     * @var AddLinkAction $add
     */
    private $add;

    /**
     * @var BrowseAction $browse
     */
    private $browse;

    public function __construct(
        ServeLibraryElementAction $serve,
        DeleteLinkAction $delete,
        AddLinkAction $add,
        BrowseAction $browse
    ) {
        $this->serve  = $serve;
        $this->delete = $delete;
        $this->add    = $add;
        $this->browse = $browse;
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
    public function deleteByIdAction(Request $request, string $libraryId, string $url): Response
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
    public function addFileAction(Request $request, string $libraryId, string $url): Response
    {
        return new JsonResponse($this->add->createAction($libraryId, base64_decode($url)));
    }

    /**
     * @return Response
     */
    public function displayOptionsAction(): Response
    {
        return new Response();
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return Response
     */
    public function browseAction(int $perPage, int $page): Response
    {
        return new JsonResponse($this->browse->handle($perPage, $page));
    }
}
