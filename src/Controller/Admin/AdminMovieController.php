<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Form\Admin\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/movies')]
class AdminMovieController extends AbstractController
{
    #[Route('', name: 'app_admin_movies')]
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAllOrderedByName();

        return $this->render('admin/movies/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/new', name: 'app_admin_movies_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $movie = new Movie();

        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($movie);
            $em->flush();

            $this->addFlash('success', 'Film został dodany.');

            return $this->redirectToRoute('app_admin_movies');
        }

        return $this->render('admin/movies/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_movies_edit', methods: ['GET', 'POST'])]
    public function edit(
        Movie $movie,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Film został zaktualizowany.');

            return $this->redirectToRoute('app_admin_movies');
        }

        return $this->render('admin/movies/edit.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_movies_delete', methods: ['POST'])]
    public function delete(Movie $movie, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_movie_' . $movie->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF.');

            return $this->redirectToRoute('app_admin_movies');
        }

        $em->remove($movie);
        $em->flush();

        $this->addFlash('success', 'Film został usunięty.');

        return $this->redirectToRoute('app_admin_movies');
    }
}