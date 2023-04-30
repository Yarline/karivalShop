<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ContentCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/payment', name: 'stripe_payment')]
    public function payment(EntityManagerInterface $em)
    {
        $stripeSecretKey = $this->getParameter('stripes_sk');
        \Stripe\Stripe::setApiKey($stripeSecretKey);

        try {
            //Calculer le prix du panier
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
        
            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $total,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        
            $output = [
                'paymentIntent'=>$paymentIntent,
                'clientSecret' => $paymentIntent->client_secret,
            ];
        
            // echo json_encode($output);
            return new JsonResponse($output);
        } catch (\Error $e) {
            // http_response_code(500);
            // echo json_encode(['error' => $e->getMessage()]);
            return new JsonResponse(['error'=> $e->getMessage()], 500);
        }
    }
}
