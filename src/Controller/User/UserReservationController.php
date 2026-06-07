<?php

namespace App\Controller\User;

use App\Entity\Reservation;
use App\Entity\User;
use App\Exception\BookingException;
use App\Repository\ReservationRepository;
use App\Repository\ReservationSeatRepository;
use App\Services\ReservationCancellationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/user/reservations')]
class UserReservationController extends AbstractController
{
    #[Route('', name: 'app_user_reservations')]
    public function index(
        ReservationRepository $reservationRepository,
        ReservationSeatRepository $reservationSeatRepository,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $reservationRepository->findByUserOrderedByNewest($user);

        $reservationSeats = [];

        foreach ($reservations as $reservation) {
            $reservationSeats[$reservation->getId()] = $reservationSeatRepository->findByReservationOrdered($reservation);
        }

        return $this->render('user/reservations/index.html.twig', [
            'reservations' => $reservations,
            'reservationSeats' => $reservationSeats,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_user_reservations_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        Request $request,
        ReservationCancellationService $cancellationService,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cancel_reservation_' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('app_user_reservations');
        }

        try {
            $cancellationService->cancel($reservation, $user);

            $this->addFlash('success', 'Rezerwacja została anulowana.');
        } catch (BookingException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_user_reservations');
    }
}