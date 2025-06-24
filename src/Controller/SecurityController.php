<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// Ce contrôleur gère la connexion et la déconnexion via le site web (formulaire HTML)
class SecurityController extends AbstractController
{
    // Route d'affichage du formulaire de connexion
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // (Optionnel) Si l'utilisateur est déjà connecté, on pourrait le rediriger :
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_home');
        // }

        // Récupère une éventuelle erreur de connexion (ex : mauvais mot de passe)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère l'email saisi précédemment pour le pré-remplir dans le champ du formulaire
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affiche le template login.html.twig avec les infos utiles
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Route de déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepte cette méthode automatiquement grâce à la configuration du firewall
        // Ce message ne sera jamais affiché, c’est une sécurité pour ne pas laisser la méthode vide
        throw new \LogicException('Cette méthode est interceptée par Symfony pour gérer la déconnexion.');
    }
}
