<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Factory\OrderFactory;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    /**
     * @var CartSessionStorage
     */
    private $cartSessionStorage;
    /**
     * @var OrderFactory
     */
    private $cartFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CartManager constructor.
     *
     * @param CartSessionStorage $cartStorage
     * @param OrderFactory $orderFactory
     * @param EntityManagerInterface $entityManager
     */

    public function __construct(CartSessionStorage $cartStorage, OrderFactory $orderFactory, EntityManagerInterface $entityManager)
    {
        $this->cartSessionStorage = $cartStorage;
        $this->cartFactory = $orderFactory;
        $this->entityManager = $entityManager;
    }

    public function getCurrentCart(): Order
    {
        $cart = $this->cartSessionStorage->getCart();

        if (!$cart) {
            $cart = $this->cartFactory->create();
        }

        return $cart;
    }
    /**
     * Persists the cart in database and session.
     *
     * @param Order $cart
     */
    public function add(Order $cart):void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $this->cartSessionStorage->setCart($cart);
    }

    public function save(Order $cart):void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function remove(Order $cart):void
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }
}
