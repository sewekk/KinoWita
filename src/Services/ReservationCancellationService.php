<?php

namespace App\Services;

use App\Entity\Reservation;
use App\Entity\User;
use App\Exception\BookingException;
use App\Repository\ReservationSeatRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationCancellationService
{
    public function __construct(
        private readonly ReservationSeatRepository $reservationSeatRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function cancel(Reservation $reservation, User $user): void
    {
        if ($reservation->getUser()?->getId() !== $user->getId()) {
            throw new BookingException('Nie możesz anulować tej rezerwacji.');
        }

        if ($reservation->getStatus() !== 'reserved') {
            throw new BookingException('Ta rezerwacja została już anulowana.');
        }

        if ($reservation->getScreening()?->getStartsAt() <= new \DateTimeImmutable()) {
            throw new BookingException('Nie można anulować rezerwacji po rozpoczęciu seansu.');
        }

        $seats = $this->reservationSeatRepository->findByReservationOrdered($reservation);

        foreach ($seats as $seat) {
            $this->em->remove($seat);
        }

        $reservation->setStatus('cancelled');

        $this->em->flush();
    }
}