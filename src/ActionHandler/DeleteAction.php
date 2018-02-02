<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\Manager\ElementManager;

class DeleteAction
{
    /**
     * @var ElementManager $manager
     */
    protected $manager;

    public function __construct(ElementManager $manager)
    {
        $this->manager = $manager;
    }

    public function deleteAction(string $libraryFileId, string $url)
    {
        return $this->manager->deleteLink($libraryFileId, $url);
    }
}
