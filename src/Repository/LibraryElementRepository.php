<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\LibraryElement;

interface LibraryElementRepository
{
    /**
     * @param string $elementId
     *
     * @return LibraryElement|null
     */
    public function findById(string $elementId);

    public function persist(LibraryElement $element);
    public function flush(LibraryElement $element = null);
    public function remove(LibraryElement $element);
}
