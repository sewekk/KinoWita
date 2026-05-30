<?php

namespace App\Controller\Staff;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STAFF')]
#[Route('/staff')]
class StaffDashboardController extends AbstractController
{
    #[Route('', name: 'app_staff_dashboard')]
    public function index(): Response
    {
        return $this->render('staff/dashboard.html.twig');
    }
}
