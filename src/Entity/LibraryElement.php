<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

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
     * @param string $url
     * @return bool
     */
    public function hasUrl(string $url): bool
    {
        $filter = function (SourceLink $link) use ($url) {
            return $link->getUrl() === $url;
        };

        return count(array_filter($this->getUrls()->toArray(), $filter)) === 1;
    }

    /**
     * @return int
     */
    public function getMaxOrderNumber()
    {
        return max(array_map(
            function (SourceLink $link) {
                return $link->getOrder();
            },
            $this->getUrls()->toArray()
        ));
    }

    /**
     * @param string $url
     * @return $this
     */
    public function pushInUrl(string $url): LibraryElement
    {
        if ($this->hasUrl($url)) {
            return $this;
        }

        $maxNumber = $this->getMaxOrderNumber();

        $link = new SourceLink($url, $this);
        $link->setOrder($maxNumber + 1);
        $this->getUrls()->add($link);

        return $this;
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
