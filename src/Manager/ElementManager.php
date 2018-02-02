<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\LibraryElement;
use App\Repository\LibraryElementRepository;
use App\Repository\SourceLinkRepository;
use App\Entity\SourceLink;

class ElementManager
{
    /**
     * @var LibraryElementRepository $libraryRepository
     */
    private $libraryRepository;

    /**
     * @var SourceLinkRepository $linkRepository
     */
    private $linkRepository;

    public function __construct(
        LibraryElementRepository $libraryElementRepository,
        SourceLinkRepository $linkRepository
    ) {
        $this->libraryRepository = $libraryElementRepository;
        $this->linkRepository    = $linkRepository;
    }

    /**
     * @param string $libraryId
     * @param string $url
     */
    public function addLink(string $libraryId, string $url)
    {
        $libraryElement = $this->libraryRepository->findById($libraryId);

        if (!$libraryElement instanceof LibraryElement) {
            $libraryElement = $this->createLibraryElement($libraryId);
        }

        $libraryElement->pushInUrl($url);
        $libraryElement->reorderAllElements();

        $this->libraryRepository->persist($libraryElement);
        $this->libraryRepository->flush($libraryElement);
    }

    /**
     * @param string $libraryId
     * @param string $url
     *
     * @return bool|SourceLink
     */
    public function deleteLink(string $libraryId, string $url): ?SourceLink
    {
        $libraryElement = $this->libraryRepository->findById($libraryId);

        if (!$libraryElement instanceof LibraryElement) {
            return null;
        }

        // pull out
        $link = $libraryElement->pullOutUrl($url);

        if (!$link) {
            return null;
        }
        
        // persist everything
        $this->linkRepository->remove($link);
        $this->linkRepository->flush($link);

        $this->libraryRepository->persist($libraryElement);
        $this->libraryRepository->flush($libraryElement);

        return $link;
    }

    /**
     * @param string $libraryId
     * @return LibraryElement
     */
    private function createLibraryElement(string $libraryId)
    {
        return new LibraryElement($libraryId);
    }
}
