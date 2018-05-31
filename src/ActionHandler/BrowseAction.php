<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\Collection\PaginatedLibraryElementResults;
use App\Repository\LibraryElementRepository;

class BrowseAction
{
    /**
     * @var LibraryElementRepository $repository
     */
    private $repository;

    public function __construct(LibraryElementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(int $perPage = 50, int $page = 1): PaginatedLibraryElementResults
    {
        return $this->repository->findMultiple(abs($perPage), abs($page));
    }
}
