<?php

namespace App\Controller;

use App\Entity\Order; // 1. ADD THIS IMPORT SO IT KNOWS WHAT AN ORDER IS
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TrackingController extends AbstractController
{
    // 2. ADD {id} TO THE ROUTE SO IT KNOWS WHICH ORDER TO LOAD
    #[Route('/tracking/{id}', name: 'app_order_tracking')]
    public function index(Order $order): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // 3. SECURITY: Stop users from typing random IDs to see other people's receipts!
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot track an order that does not belong to you.');
        }

        // 4. PASS THE REAL ORDER TO THE TEMPLATE!
        return $this->render('order/tracking.html.twig', [
            'order' => $order, 
            'trackingNumber' => $order->getTrackingNumber() ?? 'JNT-88492011PH',
            'eta' => 'Today, 4:30 PM'
        ]);
    }

    // Task 6.1: Mock Logistics Provider API (Simulating J&T Express / Lalamove)
    #[Route('/api/mock-logistics-tracking', name: 'api_mock_tracking', methods: ['GET'])]
    public function mockTrackingApi(): JsonResponse
    {
        // Simulating the coordinates of a delivery rider currently in transit
        return new JsonResponse([
            'status' => 'success',
            'courier' => 'J&T Express',
            'current_status' => 'Out for Delivery',
            'driver_name' => 'Juan Dela Cruz',
            'current_location' => [
                'lat' => 10.3157, // Latitude (Cebu City area)
                'lng' => 123.8854 // Longitude
            ],
            'last_update' => date('h:i A')
        ]);
    }
}