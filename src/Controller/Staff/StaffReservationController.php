<?php

namespace App\Controller\Staff;

use App\Entity\Cinema;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\ReservationSeatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STAFF')]
#[Route('/staff/reservations')]
class StaffReservationController extends AbstractController
{
    #[Route('', name: 'app_staff_reservations')]
    public function index(
        ReservationRepository $reservationRepository,
        ReservationSeatRepository $reservationSeatRepository,
    ): Response {
        $cinema = $this->getAssignedCinema();

        if (!$cinema) {
            $this->addFlash('error', 'Nie masz przypisanej placówki. Skontaktuj się z administratorem.');

            return $this->redirectToRoute('app_staff_dashboard');
        }

        $reservations = $reservationRepository->findByCinemaOrderedByNewest($cinema);

        $reservationSeats = [];

        foreach ($reservations as $reservation) {
            $reservationSeats[$reservation->getId()] = $reservationSeatRepository->findByReservationOrdered($reservation);
        }

        return $this->render('staff/reservations/index.html.twig', [
            'cinema' => $cinema,
            'reservations' => $reservations,
            'reservationSeats' => $reservationSeats,
        ]);
    }

    private function getAssignedCinema(): ?Cinema
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user->getAssignedCinema();
    }
}