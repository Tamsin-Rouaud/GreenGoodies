<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'isValidated' => false
        ]);

        $total = 0;

        if ($order) {
            foreach ($order->getOrderItems() as $item) {
                $total += $item->getQuantity() * $item->getUnitPrice();
            }
        }

        return $this->render('cart/index.html.twig', [
            'order' => $order,
            'totalPrice' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $quantity = (int) $request->request->get('quantity');

        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'isValidated' => false
        ]);

        if (!$order) {
            $order = new Order();
            $order->setUser($user);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setTotalPrice(0);
            $order->setIsValidated(false);
            $em->persist($order);
            $em->flush();
        }

        $orderItem = $em->getRepository(OrderItem::class)->findOneBy([
            'order' => $order,
            'product' => $product,
        ]);

        if ($quantity <= 0 && $orderItem) {
            $em->remove($orderItem);
        } elseif ($orderItem) {
            $orderItem->setQuantity($quantity);
        } else {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setUnitPrice($product->getPrice());
            $em->persist($orderItem);
        }

        $order->updateTotalPrice();
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'clear_cart')]
    public function clear(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'isValidated' => false
        ]);

        if ($order) {
            foreach ($order->getOrderItems() as $item) {
                $em->remove($item);
            }

            $order->setTotalPrice(0);
            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/confirm', name: 'confirm_order', methods: ['POST'])]
    public function confirmOrder(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'isValidated' => false
        ]);

        if (!$order || $order->isValidated() || count($order->getOrderItems()) === 0) {
            $this->addFlash('error', 'Aucune commande en cours à valider.');
            return $this->redirectToRoute('app_cart');
        }

        $total = 0;
        foreach ($order->getOrderItems() as $item) {
            $total += $item->getQuantity() * $item->getUnitPrice();
        }

        $order->setTotalPrice($total);
        $order->setIsValidated(true);
        $order->setCreatedAt(new \DateTimeImmutable());

        $em->flush();

        // ❌ plus de création de panier ici → il sera créé automatiquement dans addToCart()

        $this->addFlash('success', 'Commande confirmée !');
        return $this->redirectToRoute('app_account');
    }
}
