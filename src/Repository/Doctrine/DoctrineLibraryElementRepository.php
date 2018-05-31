<?php declare(strict_types = 1);

namespace App\Repository\Doctrine;

use App\Collection\PaginatedLibraryElementResults;
use App\Entity\LibraryElement;
use App\Repository\LibraryElementRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineLibraryElementRepository extends ServiceEntityRepository implements LibraryElementRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LibraryElement::class);
    }

    /**
     * @inheritdoc
     */
    public function findById(string $elementId): ?LibraryElement
    {
        return $this->find($elementId);
    }

    /**
     * @inheritdoc
     */
    public function findMultiple(int $maxPerPage = 50, int $page = 1): PaginatedLibraryElementResults
    {
        $query = $this->createQueryBuilder('library');

        $countQuery = clone $query;
        $countQuery->select('count(library.id)');
        $maxPages = (int) round($countQuery->getQuery()->getSingleScalarResult() / $maxPerPage, 0, PHP_ROUND_HALF_UP);

        $query
            ->setMaxResults($maxPerPage)
            ->setFirstResult($maxPerPage * ($page - 1));

        return new PaginatedLibraryElementResults(
            $query->getQuery()->getResult(),
            $maxPerPage,
            $page,
            $maxPages
        );
    }

    public function persist(LibraryElement $element)
    {
        $this->getEntityManager()->persist($element);
    }

    public function flush(LibraryElement $element = null)
    {
        $this->getEntityManager()->flush($element);
    }

    public function remove(LibraryElement $element)
    {
        $this->getEntityManager()->remove($element);
    }
}
