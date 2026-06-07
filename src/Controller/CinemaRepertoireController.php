<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Repository\ScreeningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Enum\MovieCategory;

class CinemaRepertoireController extends AbstractController
{
    #[Route('/cinemas/{id}/repertoire', name: 'app_cinema_repertoire')]
    public function index(Cinema $cinema, ScreeningRepository $screeningRepository, Request $request): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $selectedCategory = trim((string) $request->query->get('category', ''));
        $selectedDate = trim((string) $request->query->get('date', ''));

        $dateFrom = null;
        $dateTo = null;

        if ($selectedDate) {
            $dateFrom = new \DateTimeImmutable($selectedDate . ' 00:00:00');
            $dateTo = new \DateTimeImmutable($selectedDate . ' 23:59:59');
        }

        $screenings = $screeningRepository->findActiveUpcomingByCinema(
            $cinema,
            $search ?: null,
            $selectedCategory ?: null,
            $dateFrom,
            $dateTo,
        );

        $movieGroups = [];

        foreach ($screenings as $screening) {
            $movie = $screening->getMovie();
            $movieId = $movie->getId();

            if (!isset($movieGroups[$movieId])) {
                $movieGroups[$movieId] = [
                    'movie' => $movie,
                    'screenings' => [],
                ];
            }

            $movieGroups[$movieId]['screenings'][] = $screening;
        }

        return $this->render('cinemas/repertoire.html.twig', [
            'cinema' => $cinema,
            'movieGroups' => $movieGroups,
            'search' => $search,
            'selectedCategory' => $selectedCategory,
            'selectedDate' => $selectedDate,
            'categories' => MovieCategory::labels(),
        ]);
    }
}