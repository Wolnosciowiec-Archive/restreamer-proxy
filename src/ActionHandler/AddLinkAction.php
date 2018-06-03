<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\Entity\LibraryElement;
use App\Entity\SourceLink;
use App\Exception\InvalidUrlException;
use App\Manager\ElementManager;
use App\ResourceHandler\ChainedHandler;
use League\Uri\Http;

/**
 * Adds a new source of file
 *
 * 1. If the libraryFileId does not exists, then it's created first time
 * 2. It adds a new source url to the existing/new libraryFileId
 */
class AddLinkAction
{
    const ACTION_NAME = 'addLink.create';

    /**
     * @var ElementManager $manager
     */
    private $manager;

    /**
     * @var ChainedHandler $resourceHandler
     */
    private $resourceHandler;

    /**
     * @param ElementManager $manager
     */
    public function __construct(ElementManager $manager, ChainedHandler $resourceHandler)
    {
        $this->manager         = $manager;
        $this->resourceHandler = $resourceHandler;
    }

    /**
     * @param string $libraryFileId
     * @param string $url
     *
     * @return array
     */
    public function createAction(string $libraryFileId, string $url): array
    {
        $parsedUrl = Http::createFromString(trim($url));
        $libraryElement = $this->manager->getLibraryRepository()->findById($libraryFileId);

        if (!$this->resourceHandler->isHandlingUrl($parsedUrl)) {
            throw InvalidUrlException::create();
        }

        return [
            'type' => 'object',
            'data' => [
                'type'   => SourceLink::class,
                'object' => $this->manager->addLink($libraryFileId, $parsedUrl),
            ],
            'action' => self::ACTION_NAME,
            'meta' => [
                'existedBefore' => $libraryElement instanceof LibraryElement && $libraryElement->hasUrl($parsedUrl)
            ],
        ];
    }
}
