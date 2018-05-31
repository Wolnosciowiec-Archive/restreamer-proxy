<?php declare(strict_types = 1);

namespace App\Repository;

use App\Collection\PaginatedLibraryElementResults;
use App\Entity\LibraryElement;

interface LibraryElementRepository
{
    /**
     * @param string $elementId
     *
     * @return LibraryElement|null
     */
    public function findById(string $elementId): ?LibraryElement;

    /**
     * Finds multiple elements and returns paginated results
     *
     * @param int $maxPerPage
     * @param int $page
     *
     * @return PaginatedLibraryElementResults
     */
    public function findMultiple(int $maxPerPage = 50, int $page = 1): PaginatedLibraryElementResults;

    public function persist(LibraryElement $element);
    public function flush(LibraryElement $element = null);
    public function remove(LibraryElement $element);
}
