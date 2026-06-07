<?php

namespace App\Controller\Staff;

use App\Entity\Cinema;
use App\Entity\Screening;
use App\Entity\User;
use App\Form\Staff\ScreeningFormType;
use App\Repository\ScreeningRepository;
use App\Services\ScreeningCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STAFF')]
#[Route('/staff/screenings')]
class StaffScreeningController extends AbstractController
{
    #[Route('', name: 'app_staff_screenings')]
    public function index(ScreeningRepository $screeningRepository): Response
    {
        $cinema = $this->getAssignedCinema();

        if (!$cinema) {
            $this->addFlash('error', 'Nie masz przypisanej placówki. Skontaktuj się z administratorem.');

            return $this->redirectToRoute('app_staff_dashboard');
        }

        return $this->render('staff/screenings/index.html.twig', [
            'cinema' => $cinema,
            'screenings' => $screeningRepository->findByCinemaOrderedByDate($cinema),
        ]);
    }

    #[Route('/new', name: 'app_staff_screenings_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ScreeningCreationService $screeningCreationService,
    ): Response {
        $cinema = $this->getAssignedCinema();

        if (!$cinema) {
            $this->addFlash('error', 'Nie masz przypisanej placówki. Skontaktuj się z administratorem.');

            return $this->redirectToRoute('app_staff_dashboard');
        }

        $screening = new Screening();
        $screening->setCinema($cinema);
        $screening->setIsActive(true);

        $form = $this->createForm(ScreeningFormType::class, $screening, [
            'cinema' => $cinema,
            'include_repeat' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repeatDays = (int) ($form->get('repeatDays')->getData() ?? 0);

            $screeningCreationService->create($screening, $repeatDays);

            $this->addFlash('success', 'Seans został dodany.');

            return $this->redirectToRoute('app_staff_screenings');
        }

        return $this->render('staff/screenings/new.html.twig', [
            'form' => $form,
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_staff_screenings_edit', methods: ['GET', 'POST'])]
    public function edit(
        Screening $screening,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $cinema = $this->getAssignedCinema();

        if (!$cinema || $screening->getCinema()?->getId() !== $cinema->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ScreeningFormType::class, $screening, [
            'cinema' => $cinema,
            'include_repeat' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $screening->setCinema($cinema);

            $em->flush();

            $this->addFlash('success', 'Seans został zaktualizowany.');

            return $this->redirectToRoute('app_staff_screenings');
        }

        return $this->render('staff/screenings/edit.html.twig', [
            'form' => $form,
            'screening' => $screening,
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_staff_screenings_delete', methods: ['POST'])]
    public function delete(
        Screening $screening,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $cinema = $this->getAssignedCinema();

        if (!$cinema || $screening->getCinema()?->getId() !== $cinema->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('delete_screening_' . $screening->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('app_staff_screenings');
        }

        $em->remove($screening);
        $em->flush();

        $this->addFlash('success', 'Seans został usunięty.');

        return $this->redirectToRoute('app_staff_screenings');
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