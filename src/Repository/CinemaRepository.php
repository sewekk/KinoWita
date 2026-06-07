<?php

namespace App\Repository;

use App\Entity\Cinema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cinema>
 */
class CinemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cinema::class);
    }

    /**
     * @return Cinema[]
     */
    public function findAllOrderedByCity(): array
    {
        return $this->createQueryBuilder('cinema')
            ->orderBy('cinema.city', 'ASC')
            ->addOrderBy('cinema.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('cinema')
            ->orderBy('cinema.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
