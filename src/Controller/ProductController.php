<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], ['id' => 'DESC']);

        return $this->render('product/productList.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product')]
    public function show(Product $product, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $cartItem = null;

        if ($user) {
            $order = $em->getRepository(Order::class)->findOneBy([
                'user' => $user,
                'isValidated' => false
            ]);

            if ($order) {
                $cartItem = $em->getRepository(OrderItem::class)->findOneBy([
                    'order' => $order,
                    'product' => $product,
                ]);
            }
        }

        return $this->render('product/productDetail.html.twig', [
            'product' => $product,
            'cartItem' => $cartItem,
        ]);
    }
}
