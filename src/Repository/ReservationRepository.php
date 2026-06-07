<?php

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByUserOrderedByNewest(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.screening', 's')
            ->join('s.movie', 'm')
            ->join('s.cinema', 'c')
            ->join('s.hall', 'h')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.startsAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCinemaOrderedByNewest(Cinema $cinema): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.screening', 's')
            ->join('s.movie', 'm')
            ->join('s.hall', 'h')
            ->join('r.user', 'u')
            ->andWhere('s.cinema = :cinema')
            ->setParameter('cinema', $cinema)
            ->orderBy('s.startsAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
