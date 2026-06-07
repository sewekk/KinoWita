<?php

namespace App\Controller;

use App\Repository\CinemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CinemaRepository $cinemaRepository): Response
    {
        $cinemas = $cinemaRepository->findAllOrderedByCity();

        return $this->render('home/index.html.twig', [
            'cinemas' => $cinemas,
        ]);
    }
}
