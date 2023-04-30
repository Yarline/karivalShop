<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ContentCart;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em)
    {
        $products = $em->getRepository(Product::class)->findAll();
        return $this->render('default/index.html.twig', [
           'products'  => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product')]
    public function product(Product $product = null, Request $r, EntityManagerInterface $em){
        // Symfony fait la conversion automatiquement du paramètre vers un objet (paramConverter)

        if($product == null){
            $this->addFlash('danger', 'Produit introuvable');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($r);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
        }

        return $this->render('default/product.html.twig', [
            'product' => $product,
            'edit' => $form->createView()
        ]);
    }

    #[Route('/cart/{id}', name: 'addProductCart')]
    public function addProductInCart(Product $product = null, EntityManagerInterface $em, Request $request):Response{
        if($product){
            $user = $this->getUser();
            if(!$user){
                return $this->redirectToRoute('app_login');
            }else{
                $user_id = $user->getId();
                $cart = $em->getRepository(Cart::class)->findActiveCart($user_id);

                if($cart){
                    //si produit existe dans le panier(fonction repo)
                    $findContentCart = $em->getRepository(ContentCart::class)->findProductInCart($product, $cart);
                    if($findContentCart){
                        $content_cart =  $findContentCart;
                        //alors on ajoute 1 à la quantité
                        $quantity = $content_cart->getQuantity() +1;
                        $content_cart->setQuantity($quantity);
                        $em->persist($content_cart);
                        $em->flush();
                    }else{
                        //sinon on crée un content cart avec la quantité
                        $content_cart = new ContentCart();
                        $content_cart->setQuantity(1);
                        $content_cart->setCart($cart);
                        $content_cart->addProduct($product); //on met à jour le content cart product
                        $content_cart->setAddedDate(new \DateTime());
                        $em->persist($content_cart);
                        $em->flush();
                    }
                }else{
                    //on créer un panier
                    $cart = new Cart();
                    $cart->setUser($user);
                    $cart->setStatus(false);
                    $em->persist($cart);
                    $em->flush();

                    //on créer un content cart
                    $content_cart = new ContentCart();
                    $content_cart->setQuantity(1);
                    $content_cart->setCart($cart);
                    $content_cart->addProduct($product); //on met à jour le content cart product
                    $content_cart->setAddedDate(new \DateTime());
                    $em->persist($content_cart);
                    $em->flush();
                }
            }
        }else{
            //flash
        }
        
        return new JsonResponse($cart);
        
    }
}
