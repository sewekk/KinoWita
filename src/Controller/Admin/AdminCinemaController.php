<?php

namespace App\Controller\Admin;

use App\Repository\CinemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Cinema;
use App\Form\Admin\CinemaFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/cinemas')]
class AdminCinemaController extends AbstractController
{
    #[Route('', name: 'app_admin_cinemas')]
    public function index(CinemaRepository $cinemaRepository): Response
    {
        $cinemas = $cinemaRepository->findAllOrderedByName();

        return $this->render('admin/cinemas/index.html.twig', [
            'cinemas' => $cinemas,
        ]);
    }

    #[Route('/new', name: 'app_admin_cinemas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cinema = new Cinema();

        $form = $this->createForm(CinemaFormType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cinema);
            $em->flush();

            $this->addFlash('success', 'Placówka kina została dodana.');

            return $this->redirectToRoute('app_admin_cinemas');
        }

        return $this->render('admin/cinemas/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_cinemas_edit', methods: ['GET', 'POST'])]
    public function edit(
        Cinema $cinema,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $form = $this->createForm(CinemaFormType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Placówka kina została zaktualizowana.');

            return $this->redirectToRoute('app_admin_cinemas');
        }

        return $this->render('admin/cinemas/edit.html.twig', [
            'form' => $form,
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_cinemas_delete', methods: ['POST'])]
    public function delete(Cinema $cinema, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_cinema_' . $cinema->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('app_admin_cinemas');
        }

        $em->remove($cinema);
        $em->flush();

        $this->addFlash('success', 'Placówka kina została usunięta.');

        return $this->redirectToRoute('app_admin_cinemas');
    }
}