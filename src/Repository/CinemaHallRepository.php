<?php

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\CinemaHall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CinemaHall>
 */
class CinemaHallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CinemaHall::class);
    }

    public function findByCinemaOrderedByName(Cinema $cinema): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.cinema = :cinema')
            ->setParameter('cinema', $cinema)
            ->orderBy('h.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
