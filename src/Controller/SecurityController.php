<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/account', name: 'app_account')]
    public function account(EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        // $cart = $em->getRepository(Cart::class)->findActiveCart($user);

        // $contentCart = $em->getRepository(ContentCart::class)->findContentCart($cart);

        // $total = 0;
        // foreach ($contentCart as $item) {
        //     $quantity = $item->getQuantity();
        //     $product = $item->getProduct()->first();
        //     $price = $product->getPrice();
            
        //     $contentCartTotal = $quantity * $price;
        //     $total += $contentCartTotal;
        // }

        return $this->render('security/account.html.twig', [
            'currentUser'  => $user,
            // 'contentCart'  => $contentCart,
            // 'total' => $total,
        ]);
    }
}
