<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\LibraryElement;
use App\Event\SourceDeletedEvent;
use App\Events;
use App\Repository\LibraryElementRepository;
use App\Repository\SourceLinkRepository;
use App\Entity\SourceLink;
use Psr\Http\Message\UriInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    public function __construct(
        LibraryElementRepository $libraryElementRepository,
        SourceLinkRepository     $linkRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->libraryRepository = $libraryElementRepository;
        $this->linkRepository    = $linkRepository;
        $this->dispatcher        = $dispatcher;
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
            $this->dispatcher->dispatch(Events::LIBRARY_LINK_DELETE, new SourceDeletedEvent($libraryElement));
            return null;
        }
        
        // persist everything
        $this->linkRepository->remove($link);
        $this->libraryRepository->persist($libraryElement);
        $this->libraryRepository->flush();
        $this->linkRepository->flush();

        $this->dispatcher->dispatch(Events::LIBRARY_LINK_DELETE, new SourceDeletedEvent($libraryElement, $link));
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
