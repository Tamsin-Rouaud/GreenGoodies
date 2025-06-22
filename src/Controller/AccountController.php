<?php

namespace App\Controller;

use App\Entity\Order;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    // #[Route('/account', name: 'app_account')]
    // public function index(OrderRepository $orderRepository): Response
    // {
    //     $user = $this->getUser();
    //     if (!$user) {
    //         return $this->redirectToRoute('app_login');
    //     }

    //     // On récupère toutes les commandes liées à l'utilisateur
    //     $orders = $orderRepository->findValidatedByUser($user);


    //     return $this->render('account/index.html.twig', [
    //         'orders' => $orders,
    //     ]);
    // }

    #[Route('/account', name: 'app_account')]
public function index(EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Utilise la méthode personnalisée pour récupérer les commandes validées
    $orders = $em->getRepository(Order::class)->findValidatedOrdersByUser($user);

    return $this->render('account/index.html.twig', [
        'orders' => $orders,
    ]);
}

}
