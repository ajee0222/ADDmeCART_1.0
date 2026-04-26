<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        // Require the user to be logged in to view the dashboard
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Mock data representing a week of sales for the chart
        $salesData = [
            'labels' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'data' => [1500, 2300, 1800, 3200, 2900, 4500, 5100],
        ];

        return $this->render('admin/index.html.twig', [
            'salesData' => $salesData,
        ]);
    }
}