<?php declare(strict_types = 1);

namespace App\Manager;

use App\Entity\LibraryElement;
use App\Event\SourceDeletedEvent;
use App\Events;
use App\Exception\InvalidLibraryNameException;
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
     *
     * @throws InvalidLibraryNameException
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
     * @return LibraryElementRepository
     */
    public function getLibraryRepository(): LibraryElementRepository
    {
        return $this->libraryRepository;
    }

    /**
     * @param string $libraryId
     * @return LibraryElement
     *
     * @throws InvalidLibraryNameException
     */
    private function createLibraryElement(string $libraryId)
    {
        $this->assertValidLibraryId($libraryId);

        return new LibraryElement($this->escapeLibraryId($libraryId));
    }

    /**
     * @param string $libraryId
     *
     * @throws InvalidLibraryNameException
     */
    private function assertValidLibraryId(string $libraryId): void
    {
        $length = \strlen($libraryId);

        if ($length < LibraryElement::ID_MIN_LENGTH || $length > LibraryElement::ID_MAX_LENGTH) {
            throw InvalidLibraryNameException::createInvalidLengthError();
        }

        if ($libraryId !== $this->escapeLibraryId($libraryId)) {
            throw InvalidLibraryNameException::createInvalidFormatError($this->escapeLibraryId($libraryId));
        }
    }

    private function escapeLibraryId(string $libraryId): string
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $libraryId);
        $clean = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $clean);
        $clean = strtolower(trim($clean, '-'));

        return preg_replace("/[\/_|+ -]+/", '-', $clean);
    }
}
