<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ContentCart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $cart = $em->getRepository(Cart::class)->findActiveCart($user);

        $contentCart = $em->getRepository(ContentCart::class)->findContentCart($cart);

        $total = 0;
        foreach ($contentCart as $item) {
            $quantity = $item->getQuantity();
            $product = $item->getProduct()->first();
            $price = $product->getPrice();
            
            $contentCartTotal = $quantity * $price;
            $total += $contentCartTotal;
        }

        return $this->render('cart/index.html.twig', [
            'contentCart'  => $contentCart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/deleteContentCart/{id}', name: 'app_deleteContentCart')]
    public function deleteContentCart(EntityManagerInterface $em, ContentCart $cc): Response
    {

        if($cc == null){
            $this->addFlash('danger', 'Catégorie introuvable');
        }
        else{
            $em->remove($cc);
            $em->flush();
        }

        $user = $this->getUser();

        $cart = $em->getRepository(Cart::class)->findActiveCart($user);

        $contentCart = $em->getRepository(ContentCart::class)->findContentCart($cart);

        $total = 0;
        foreach ($contentCart as $item) {
            $quantity = $item->getQuantity();
            $product = $item->getProduct()->first();
            $price = $product->getPrice();
            
            $contentCartTotal = $quantity * $price;
            $total += $contentCartTotal;
        }

        return $this->render('cart/index.html.twig', [
            'contentCart'  => $contentCart,
            'total' => $total,
        ]);
    }
}
