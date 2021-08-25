<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartManager $cartManager, Request $request): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->add($cart);

            return $this->redirectToRoute('cart');
        }
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/add", name="item_add")
     */
    public function addToDatabase(CartManager $cartManager)
    {
        $cart = $cartManager->getCurrentCart();
        $cartManager->save($cart);
        $cartManager->remove($cart);
        $this->addFlash("Success", "Check out successful");
        return $this->redirectToRoute('cart');
    }
//        /**
//         * @Route ("/cart/delete/{id}", name="item_delete")
//         */
//        public function deleteItem($id)
//        {
//            $item = $this->getDoctrine()->getRepository(OrderItem::class)->find($id);
//            $manager = $this->getDoctrine()->getManager();
//            $manager->remove($item);
//            $manager->flush();
//
//            $this->addFlash("Success", "Product has been deleted from cart");
//            return $this->redirectToRoute("cart");
//        }
}
