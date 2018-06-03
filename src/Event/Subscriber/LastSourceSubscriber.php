<?php declare(strict_types = 1);

namespace App\Event\Subscriber;

use App\Event\SourceDeletedEvent;
use App\Events;
use App\Repository\LibraryElementRepository;
use App\Repository\SourceLinkRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastSourceSubscriber implements EventSubscriberInterface
{
    /**
     * @var LibraryElementRepository $libraryRepository
     */
    private $libraryRepository;

    /**
     * @var SourceLinkRepository $linkRepository
     */
    private $linkRepository;

    public function __construct(LibraryElementRepository $repository, SourceLinkRepository $linkRepository)
    {
        $this->libraryRepository = $repository;
        $this->linkRepository    = $linkRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::LIBRARY_LINK_DELETE => 'onLinkDeleted'
        ];
    }

    public function onLinkDeleted(SourceDeletedEvent $event)
    {
        if (!$event->getLibraryElement()->getUrls()->isEmpty()) {
            return;
        }

        // delete the whole library element in case that no any sources are there
        $this->libraryRepository->remove($event->getLibraryElement());
        $this->libraryRepository->flush();
        $this->linkRepository->flush();
    }
}
