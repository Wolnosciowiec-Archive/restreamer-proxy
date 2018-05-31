<?php declare(strict_types = 1);

namespace App\Collection;

use App\Entity\LibraryElement;

class PaginatedLibraryElementResults implements \JsonSerializable
{
    /**
     * @var LibraryElement[]
     */
    private $results;

    /**
     * @var int $perPage
     */
    private $perPage;

    /**
     * @var int $page
     */
    private $page;

    /**
     * @var int $maxPages
     */
    private $maxPages;

    public function __construct(array $results, int $perPage, int $page, int $maxPages)
    {
        $this->results  = $results;
        $this->perPage  = $perPage;
        $this->page     = $page;
        $this->maxPages = $maxPages;
    }

    /**
     * @return int
     */
    public function getMaxPages(): int
    {
        return $this->maxPages;
    }

    /**
     * @return LibraryElement[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'elementsPerPage' => $this->perPage,
            'results'         => $this->results,
            'page'            => $this->page,
            'maxPages'        => $this->maxPages
        ];
    }
}
