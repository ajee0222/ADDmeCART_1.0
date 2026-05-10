<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    // The catalog is public. No IsGranted needed here.
    #[Route('/products', name: 'app_product_catalog')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // ✅ MUST come BEFORE /product/{id} to avoid routing conflict
    #[Route('/product/new', name: 'app_product_new')]
    #[IsGranted('ROLE_SELLER', message: 'Only registered sellers can add products.')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSeller($this->getUser());

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Your product was successfully added to your store!');

            return $this->redirectToRoute('app_product_catalog');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // ✅ Accepts GET (view page) and POST (submit review)
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Guard: only logged-in users can submit reviews
            if (!$this->getUser()) {
                $this->addFlash('error', 'You must be logged in to submit a review.');
                return $this->redirectToRoute('app_login');
            }

            $review->setProduct($product);
            $review->setUser($this->getUser());
            $review->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($review);

            // Recalculate average star rating
            // We flush first so the new review is included in getReviews()
            $entityManager->flush();

            $allReviews = $product->getReviews();
            $totalRating = 0;
            foreach ($allReviews as $r) {
                $totalRating += $r->getRating();
            }
            if (count($allReviews) > 0) {
                $product->setStarRating($totalRating / count($allReviews));
            }

            $entityManager->flush();

            $this->addFlash('success', 'Thank you for your review!');

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviewForm' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    #[IsGranted('ROLE_SELLER')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($product->getSeller() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit a product you do not own.');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function delete(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($product->getSeller() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot delete a product you do not own.');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product deleted successfully!');
        }

        return $this->redirectToRoute('app_profile');
    }
}