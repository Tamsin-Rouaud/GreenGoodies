<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiProductController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/products', name: 'api_products', methods: ['GET'])]
    public function list(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifie si l'accès API est activé
if (!$user->isApiAccessEnabled()) {
    throw new AccessDeniedHttpException('Accès API non activé. Activez-le dans votre profil.');
}


        $products = $productRepository->findAll();
        $json = $serializer->serialize($products, 'json', ['groups' => ['product:read']]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
