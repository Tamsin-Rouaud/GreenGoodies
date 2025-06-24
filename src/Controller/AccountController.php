<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

// Ce contrôleur gère les actions liées au compte utilisateur : affichage, suppression, activation API
class AccountController extends AbstractController
{
    // Affiche la page "Mon compte"
    #[Route('/account', name: 'app_account')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupère uniquement les commandes validées de l'utilisateur
        $orders = $em->getRepository(Order::class)->findValidatedOrdersByUser($user);

        return $this->render('account/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // Supprime le compte utilisateur ainsi que ses commandes
    #[Route('/account/delete', name: 'account_delete', methods: ['POST'])]
    public function deleteAccount(
        EntityManagerInterface $em,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification du token CSRF
        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_account', $submittedToken))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_account');
        }

        // Suppression manuelle des commandes (si pas en cascade REMOVE)
        foreach ($user->getOrders() as $order) {
            $em->remove($order);
        }

        // Suppression du compte utilisateur
        $em->remove($user);
        $em->flush();

        // Déconnexion propre de l'utilisateur (sécurité + session)
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        $this->addFlash('success', 'Votre compte a bien été supprimé.');

        return $this->redirectToRoute('app_home');
    }

    // Active ou désactive l'accès API de l'utilisateur
    #[Route('/account/api-access/toggle', name: 'account_toggle_api_access', methods: ['POST'])]
    public function toggleApiAccess(EntityManagerInterface $em, Security $security): RedirectResponse
    {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Inversion de l'état du champ isApiAccessEnabled
        $user->setIsApiAccessEnabled(!$user->isApiAccessEnabled());
        $em->flush();

        return $this->redirectToRoute('app_account');
    }
}
