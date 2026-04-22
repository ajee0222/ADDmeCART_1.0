<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        // Requires user to be logged in
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('checkout/index.html.twig');
    }

    #[Route('/checkout/process', name: 'app_checkout_process', methods: ['POST'])]
    public function process(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        // --- BUSINESS RULE 1: 300-Item Limit Check ---
        $totalItemsInCart = 10; // Placeholder for actual cart count
        if ($totalItemsInCart > 300) {
            $this->addFlash('error', 'Cart limit exceeded. You cannot purchase more than 300 items at once.');
            return $this->redirectToRoute('app_checkout');
        }

        // --- BUSINESS RULE 2: Mandatory 4-Digit PIN Security ---
        $inputPin = $request->request->get('security_pin');
        
        if (!password_verify((string) $inputPin, $user->getSecurityPin())) {
            $this->addFlash('error', 'Incorrect 4-Digit PIN. Transaction halted.');
            return $this->redirectToRoute('app_checkout');
        }

        // --- Create the Order ---
        $order = new Order();
        $order->setUser($user);
        $order->setOrderStatus('Processing');
        $order->setTotalAmount(999.99); // Placeholder for subtotal
        
        $entityManager->persist($order);
        $entityManager->flush();

        $this->addFlash('success', 'PIN Verified! Your order has been placed securely.');
        return $this->redirectToRoute('app_home');
    }
}