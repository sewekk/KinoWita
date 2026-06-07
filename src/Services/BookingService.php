<?php

namespace App\Services;

use App\Entity\Reservation;
use App\Entity\ReservationSeat;
use App\Entity\Screening;
use App\Entity\User;
use App\Exception\BookingException;
use App\Repository\ReservationSeatRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    public function __construct(
        private readonly ReservationSeatRepository $reservationSeatRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function isScreeningAvailable(Screening $screening): bool
    {
        return $screening->isActive()
            && $screening->getStartsAt() >= new \DateTimeImmutable();
    }

    public function book(Screening $screening, User $user, array $selectedSeats): void
    {
        if (!$this->isScreeningAvailable($screening)) {
            throw new BookingException('Ten seans nie jest już dostępny.');
        }

        if (empty($selectedSeats)) {
            throw new BookingException('Wybierz przynajmniej jedno miejsce.');
        }

        $hall = $screening->getHall();

        if (!$hall) {
            throw new BookingException('Sala dla tego seansu nie istnieje.');
        }

        $reservedSeats = $this->reservationSeatRepository->findReservedSeatsByScreening($screening);
        $selectedMap = [];

        $reservation = new Reservation();
        $reservation
            ->setUser($user)
            ->setScreening($screening)
            ->setStatus('reserved');

        foreach ($selectedSeats as $seatValue) {
            [$rowNumber, $seatNumber] = $this->parseSeat((string) $seatValue);

            if (
                $rowNumber < 1 ||
                $seatNumber < 1 ||
                $rowNumber > $hall->getRowsCount() ||
                $seatNumber > $hall->getSeatsPerRow()
            ) {
                throw new BookingException('Jedno z wybranych miejsc jest nieprawidłowe.');
            }

            if (isset($reservedSeats[$rowNumber][$seatNumber])) {
                throw new BookingException('Jedno z wybranych miejsc jest już zajęte.');
            }

            $seatKey = $rowNumber . ':' . $seatNumber;

            if (isset($selectedMap[$seatKey])) {
                throw new BookingException('Nie możesz wybrać tego samego miejsca kilka razy.');
            }

            $selectedMap[$seatKey] = true;

            $reservationSeat = new ReservationSeat();
            $reservationSeat
                ->setReservation($reservation)
                ->setScreening($screening)
                ->setRowNumber($rowNumber)
                ->setSeatNumber($seatNumber);

            $this->em->persist($reservationSeat);
        }

        try {
            $this->em->persist($reservation);
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new BookingException('Jedno z miejsc zostało właśnie zarezerwowane przez inną osobę.');
        }
    }

    private function parseSeat(string $seatValue): array
    {
        $parts = explode(':', $seatValue);

        if (count($parts) !== 2) {
            throw new BookingException('Nieprawidłowe miejsce.');
        }

        return array_map('intval', $parts);
    }
}