<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};

/**
 * Shows a welcome page
 */
class DefaultController extends Controller
{
    public function indexAction(): Response
    {
        return new JsonResponse('Hello, this is restreamer-proxy. See https://github.com/Wolnosciowiec/restreamer-proxy for more details.');
    }
}
