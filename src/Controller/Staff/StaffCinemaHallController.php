<?php

namespace App\Controller\Staff;

use App\Entity\Cinema;
use App\Entity\CinemaHall;
use App\Entity\User;
use App\Form\Staff\CinemaHallFormType;
use App\Repository\CinemaHallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STAFF')]
#[Route('/staff/halls')]
class StaffCinemaHallController extends AbstractController
{
    #[Route('', name: 'app_staff_halls')]
    public function index(CinemaHallRepository $hallRepository): Response
    {
        $cinema = $this->getAssignedCinema();

        if (!$cinema) {
            $this->addFlash('error', 'Nie masz przypisanej placówki. Skontaktuj się z administratorem.');

            return $this->redirectToRoute('app_staff_dashboard');
        }

        return $this->render('staff/halls/index.html.twig', [
            'cinema' => $cinema,
            'halls' => $hallRepository->findByCinemaOrderedByName($cinema),
        ]);
    }

    #[Route('/new', name: 'app_staff_halls_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cinema = $this->getAssignedCinema();

        if (!$cinema) {
            $this->addFlash('error', 'Nie masz przypisanej placówki. Skontaktuj się z administratorem.');

            return $this->redirectToRoute('app_staff_dashboard');
        }

        $hall = new CinemaHall();
        $hall->setCinema($cinema);

        $form = $this->createForm(CinemaHallFormType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($hall);
            $em->flush();

            $this->addFlash('success', 'Sala została dodana.');

            return $this->redirectToRoute('app_staff_halls');
        }

        return $this->render('staff/halls/new.html.twig', [
            'form' => $form,
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_staff_halls_edit', methods: ['GET', 'POST'])]
    public function edit(
        CinemaHall $hall,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $cinema = $this->getAssignedCinema();

        if (!$cinema || $hall->getCinema()?->getId() !== $cinema->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CinemaHallFormType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Sala została zaktualizowana.');

            return $this->redirectToRoute('app_staff_halls');
        }

        return $this->render('staff/halls/edit.html.twig', [
            'form' => $form,
            'hall' => $hall,
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_staff_halls_delete', methods: ['POST'])]
    public function delete(CinemaHall $hall, Request $request, EntityManagerInterface $em): Response
    {
        $cinema = $this->getAssignedCinema();

        if (!$cinema || $hall->getCinema()?->getId() !== $cinema->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('delete_hall_' . $hall->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('app_staff_halls');
        }

        $em->remove($hall);
        $em->flush();

        $this->addFlash('success', 'Sala została usunięta.');

        return $this->redirectToRoute('app_staff_halls');
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