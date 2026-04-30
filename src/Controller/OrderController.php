<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CUSTOMER')]
class OrderController extends AbstractController
{
    // THIS FIXES BUG 2: The Order History / Receipts Page
    #[Route('/my-orders', name: 'app_user_orders')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch only the orders belonging to the logged-in user, sorted by newest first
        $orders = $entityManager->getRepository(Order::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // THIS FIXES BUG 3: Dynamic Order Tracking
    #[Route('/tracking/{id}', name: 'app_order_tracking')]
    public function track(Order $order): Response
    {
        // Security Check: Make sure users can't track someone else's order!
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot track an order that does not belong to you.');
        }

        return $this->render('order/track.html.twig', [
            'order' => $order,
        ]);
    }
}