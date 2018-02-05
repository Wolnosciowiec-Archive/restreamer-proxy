<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\Entity\SourceLink;
use App\Manager\ElementManager;

class DeleteLinkAction
{
    const ACTION_NAME = 'deleteLink.delete';

    /**
     * @var ElementManager $manager
     */
    protected $manager;

    /**
     * @param ElementManager $manager
     */
    public function __construct(ElementManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $libraryFileId
     * @param string $url
     *
     * @return array
     */
    public function deleteAction(string $libraryFileId, string $url)
    {
        $object = $this->manager->deleteLink($libraryFileId, $url);
        
        return [
            'meta'   => [
                'existedBefore' => $object instanceof SourceLink
            ],
            'action' => self::ACTION_NAME,
            'type'   => SourceLink::class,
            'object' => $object
        ];
    }
}
