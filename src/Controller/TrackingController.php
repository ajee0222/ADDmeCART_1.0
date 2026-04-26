<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TrackingController extends AbstractController
{
    // Task 6.2: The Tracking UI Route
    #[Route('/tracking', name: 'app_order_tracking')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Passing a mock Order ID for the prototype
        return $this->render('order/tracking.html.twig', [
            'trackingNumber' => 'JNT-88492011PH',
            'eta' => 'Today, 4:30 PM'
        ]);
    }

    // Task 6.1: Mock Logistics Provider API (Simulating J&T Express / Lalamove)
    #[Route('/api/mock-logistics-tracking', name: 'api_mock_tracking', methods: ['GET'])]
    public function mockTrackingApi(): JsonResponse
    {
        // Simulating the coordinates of a delivery rider currently in transit
        // (These are coordinates for a random street to simulate GPS movement)
        return new JsonResponse([
            'status' => 'success',
            'courier' => 'J&T Express',
            'current_status' => 'Out for Delivery',
            'driver_name' => 'Juan Dela Cruz',
            'current_location' => [
                'lat' => 10.3157, // Latitude (e.g., Cebu City area)
                'lng' => 123.8854 // Longitude
            ],
            'last_update' => date('h:i A')
        ]);
    }
}