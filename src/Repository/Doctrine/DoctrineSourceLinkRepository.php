<?php declare(strict_types = 1);

namespace App\Repository\Doctrine;

use App\Entity\SourceLink;
use App\Repository\SourceLinkRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineSourceLinkRepository extends ServiceEntityRepository implements SourceLinkRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SourceLink::class);
    }

    public function persist(SourceLink $element)
    {
    	$this->getEntityManager()->persist($element);
    }

    public function flush(SourceLink $element = null)
    {
    	$this->getEntityManager()->flush($element);
    }

    public function remove(SourceLink $element)
    {
	    $this->getEntityManager()->remove($element);
    }
}
