<?php declare(strict_types = 1);

namespace App\Event;

use App\Entity\LibraryElement;
use App\Entity\SourceLink;

class SourceDeletedEvent extends AppEvent
{
    /**
     * @var SourceLink|null $link
     */
    private $link;

    /**
     * @var LibraryElement $libraryElement
     */
    private $libraryElement;

    public function __construct(LibraryElement $libraryElement, SourceLink $link = null)
    {
        $this->link           = $link;
        $this->libraryElement = $libraryElement;

        parent::__construct();
    }

    /**
     * @return SourceLink
     */
    public function getLink(): ?SourceLink
    {
        return $this->link;
    }

    /**
     * @return LibraryElement
     */
    public function getLibraryElement(): LibraryElement
    {
        return $this->libraryElement;
    }
}
