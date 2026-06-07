<?php

namespace App\Controller\User;

use App\Entity\Screening;
use App\Entity\User;
use App\Exception\BookingException;
use App\Repository\ReservationSeatRepository;
use App\Services\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookingController extends AbstractController
{
    #[Route('/screenings/{id}/booking', name: 'app_booking_screening', methods: ['GET', 'POST'])]
    public function booking(
        Screening $screening,
        Request $request,
        ReservationSeatRepository $reservationSeatRepository,
        BookingService $bookingService,
    ): Response {
        $user = $this->getUser();

        if (!$bookingService->isScreeningAvailable($screening)) {
            $this->addFlash('error', 'Ten seans nie jest już dostępny.');

            return $this->redirectToRoute('app_cinema_repertoire', [
                'id' => $screening->getCinema()?->getId(),
            ]);
        }

        $hall = $screening->getHall();
        $reservedSeats = $reservationSeatRepository->findReservedSeatsByScreening($screening);

        if ($request->isMethod('POST')) {
            if (!$user instanceof User) {
                $this->addFlash('error', 'Aby zarezerwować miejsce, zaloguj się lub załóż konto.');

                return $this->redirectToRoute('app_booking_screening', [
                    'id' => $screening->getId(),
                ]);
            }

            if (!$this->isCsrfTokenValid('book_screening_' . $screening->getId(), $request->request->get('_token'))) {
                $this->addFlash('error', 'Nieprawidłowy token CSRF.');

                return $this->redirectToRoute('app_booking_screening', [
                    'id' => $screening->getId(),
                ]);
            }

            try {
                $bookingService->book(
                    $screening,
                    $user,
                    $request->request->all('seats')
                );

                $this->addFlash('success', 'Rezerwacja została utworzona.');
            } catch (BookingException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }

            return $this->redirectToRoute('app_booking_screening', [
                'id' => $screening->getId(),
            ]);
        }

        return $this->render('user/booking/screening.html.twig', [
            'screening' => $screening,
            'hall' => $hall,
            'reservedSeats' => $reservedSeats,
            'canBook' => $user instanceof User,
        ]);
    }
}