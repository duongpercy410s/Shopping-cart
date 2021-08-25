<?php

namespace App\Storage;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorage {
    /**
     * The session storage.
     *
     * @var SessionInterface
     */
    private $session;
    /**
     * The cart repository.
     *
     * @var OrderRepository
     */
    private $cartRepository;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';

    /**
     * CartSessionStorage constructor.
     *
     * @param SessionInterface $session
     * @param OrderRepository $cartRepository
     */

    public function __construct(SessionInterface $session, OrderRepository $cartRepository)
    {
        $this->session = $session;
        $this->cartRepository = $cartRepository;
    }

    public function getCart():? Order
    {
        return $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART
        ]);
    }

    public function setCart(Order $cart):void
    {
        $this->session->set(self::CART_KEY_NAME, $cart->getId());
    }

    private function getCartId(): ?int
    {
        return $this->session->get(self::CART_KEY_NAME);
    }

    public function deleteCart(Order $cart)
    {
        $this->session->remove(self::CART_KEY_NAME);
    }
}