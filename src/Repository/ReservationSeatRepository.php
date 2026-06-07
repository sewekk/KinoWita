<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\ReservationSeat;
use App\Entity\Screening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationSeat>
 */
class ReservationSeatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationSeat::class);
    }

    public function findReservedSeatsByScreening(Screening $screening): array
    {
        $seats = $this->createQueryBuilder('rs')
            ->select('rs.rowNumber', 'rs.seatNumber')
            ->join('rs.reservation', 'r')
            ->andWhere('rs.screening = :screening')
            ->andWhere('r.status = :status')
            ->setParameter('screening', $screening)
            ->setParameter('status', 'reserved')
            ->getQuery()
            ->getArrayResult();

        $reservedSeats = [];

        foreach ($seats as $seat) {
            $reservedSeats[$seat['rowNumber']][$seat['seatNumber']] = true;
        }

        return $reservedSeats;
    }

    public function findByReservationOrdered(Reservation $reservation): array
    {
        return $this->createQueryBuilder('rs')
            ->andWhere('rs.reservation = :reservation')
            ->setParameter('reservation', $reservation)
            ->orderBy('rs.rowNumber', 'ASC')
            ->addOrderBy('rs.seatNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
