<?php declare(strict_types = 1);

namespace App\Repository\Doctrine;

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
    public function findById(string $elementId)
    {
        return $this->find($elementId);
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
