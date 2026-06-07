<?php

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\Screening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Screening>
 */
class ScreeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screening::class);
    }

    public function findByCinemaOrderedByDate(Cinema $cinema): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.cinema = :cinema')
            ->setParameter('cinema', $cinema)
            ->orderBy('s.startsAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
