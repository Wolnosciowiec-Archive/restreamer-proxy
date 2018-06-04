<?php declare(strict_types = 1);

namespace App\Controller;

use App\ActionHandler\ListSupportedHostingsAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SupportedHostingsController extends Controller
{
    /**
     * @var ListSupportedHostingsAction $handler
     */
    private $handler;

    public function __construct(ListSupportedHostingsAction $handler)
    {
        $this->handler = $handler;
    }

    public function listAction(): Response
    {
        return new JsonResponse($this->handler->handle());
    }
}
