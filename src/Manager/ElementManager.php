<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\LibraryElement;
use App\Repository\LibraryElementRepository;
use App\Repository\SourceLinkRepository;
use App\Entity\SourceLink;
use Psr\Http\Message\UriInterface;

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
     * @param UriInterface $url
     *
     * @return null|SourceLink
     */
    public function addLink(string $libraryId, UriInterface $url): ?SourceLink
    {
        $libraryElement = $this->libraryRepository->findById($libraryId);

        if (!$libraryElement instanceof LibraryElement) {
            $libraryElement = $this->createLibraryElement($libraryId);
        }

        $newLink = $libraryElement->pushInUrl($url);
        $libraryElement->reorderAllElements();

        $this->linkRepository->persist($newLink);
        $this->libraryRepository->persist($libraryElement);

        $this->linkRepository->flush();
        $this->libraryRepository->flush();

        return $newLink;
    }

    /**
     * @param string $libraryId
     * @param string $url
     *
     * @return null|SourceLink
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

        if ($libraryElement->getUrls()->isEmpty()) {
            // delete the whole library element in case that no any sources are there
            $this->libraryRepository->remove($libraryElement);
            $this->libraryRepository->flush();
            $this->linkRepository->flush();

            return $link;
        }

        $this->libraryRepository->persist($libraryElement);
        $this->libraryRepository->flush();
        $this->linkRepository->flush();

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

    /**
     * @return LibraryElementRepository
     */
    public function getLibraryRepository(): LibraryElementRepository
    {
        return $this->libraryRepository;
    }
}
