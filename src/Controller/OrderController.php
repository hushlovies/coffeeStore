<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(SessionInterface $session, ProductRepository $productsRepository)
    {
        $cart = $session->get("cart", []);

        $cartContent = [];
        $sum = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productsRepository->find($id);
            $cartContent[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
            $sum += $product->getPrice() * $quantity;
        }

        return $this->render('order/index.html.twig', compact("cartContent", "sum"));
    }



    #[Route('/order/add/{id}', name: 'add')]
    public function add($id, SessionInterface $session)
    {
        $cart = $session->get("cart", []);


        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set("cart", $cart);
        return $this->redirectToRoute("order");
    }

    #[Route('/order/remove/{id}', name: 'remove')]
    public function remove(Product $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }


        $session->set("cart", $cart);

        return $this->redirectToRoute("order");
    }

    #[Route('/order/delete/{id}', name: 'delete')]
    public function delete(Product $product, SessionInterface $session)
    {

        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }


        $session->set("cart", $cart);

        return $this->redirectToRoute("order");
    }

    #[Route('/order/deleteAll', name: 'deleteAll')]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("order");
    }
}
