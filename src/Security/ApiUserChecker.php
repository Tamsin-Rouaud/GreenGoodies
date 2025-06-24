<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

// Cette classe permet de vérifier certaines conditions avant qu’un utilisateur puisse se connecter à l’API
class ApiUserChecker implements UserCheckerInterface
{
    // Cette méthode est appelée AVANT l'authentification complète
    public function checkPreAuth(UserInterface $user): void
    {
        // On s'assure que l'objet utilisateur est bien de type App\Entity\User
        if (!$user instanceof User) {
            return;
        }

        // Si l'accès API n’est pas activé pour l’utilisateur...
        if (!$user->isApiAccessEnabled()) {
            // ... on empêche la connexion API et on affiche un message d’erreur personnalisé
            throw new CustomUserMessageAccountStatusException('Accès API non activé. Activez-le dans votre profil.');
        }
    }

    // Cette méthode est appelée APRÈS l'authentification, ici on ne fait rien
    public function checkPostAuth(UserInterface $user): void
    {
        // Rien ici pour l'instant
    }
}
