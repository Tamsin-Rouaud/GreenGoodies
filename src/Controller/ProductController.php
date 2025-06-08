<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Entity\Product;
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
public function show(Product $product): Response
{
    return $this->render('product/productDetail.html.twig', [
        'product' => $product,
    ]);
}


}
