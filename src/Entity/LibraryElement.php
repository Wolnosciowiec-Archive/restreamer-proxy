<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Psr\Http\Message\UriInterface;

/**
 * Represents an element - eg. "CDE0FIQuOlpGlaJuujZKlHd8hmjIbG"
 * that contains a list of urls
 */
class LibraryElement
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var SourceLink[] $urls
     */
    private $urls;

    public function __construct(string $id, $urls = null)
    {
        $this->id   = $id;
        $this->urls = $urls ?? new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return SourceLink[]|PersistentCollection
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderedUrls(): ArrayCollection
    {
        $urls = $this->getUrls()->toArray();

        usort($urls, function (SourceLink $a, SourceLink $b) {
            return ($a->getOrder() <=> $b->getOrder());
        });

        return new ArrayCollection($urls);
    }

    /**
     * @param UriInterface $url
     * @return bool
     */
    public function hasUrl(UriInterface $url): bool
    {
        return $this->findLinkByUrl($url) instanceof SourceLink;
    }

    /**
     * @param UriInterface $url
     *
     * @return SourceLink|null
     */
    public function findLinkByUrl(UriInterface $url): ?SourceLink
    {
        $filtered = array_filter(
            $this->getUrls()->toArray(),
            function (SourceLink $link) use ($url) {
                return (string) $link->getUrl() === (string) $url;
            }
        );

        $filtered = array_values($filtered);
        return $filtered[0] ?? null;
    }

    /**
     * @return int
     */
    public function getMaxOrderNumber()
    {
        $numbers = array_map(
            function (SourceLink $link) {
                return $link->getOrder();
            },
            $this->getUrls()->toArray()
        );

        if (count($numbers) === 0) {
            return 0;
        }

        return max($numbers);
    }

    /**
     * @param string $url
     * @return SourceLink
     */
    public function pushInUrl(UriInterface $url): SourceLink
    {
        if ($this->hasUrl($url)) {
            return $this->findLinkByUrl($url);
        }

        $maxNumber = $this->getMaxOrderNumber();

        $link = new SourceLink($url, $this);
        $link->setOrder($maxNumber + 1);
        $this->getUrls()->add($link);

        return $link;
    }

    /**
     * Remove URL from the library element
     *
     * @param string $url
     * @return SourceLink|null
     */
    public function pullOutUrl(string $url): ?SourceLink
    {
        /** @var SourceLink $link */
        foreach ($this->getUrls()->toArray() as $link) {
            if ((string) $link->getUrl() === $url) {
                $this->getUrls()->removeElement($url);
                return $link;
            }
        }

        return null;
    }

    /**
     * Reorder all elements
     *
     * @return LibraryElement
     */
    public function reorderAllElements(): LibraryElement
    {
        $num = 0;

        /** @var SourceLink $link */
        foreach ($this->getOrderedUrls()->toArray() as $link) {
            $num++;
            $link->setOrder($num);
        }

        return $this;
    }
}
