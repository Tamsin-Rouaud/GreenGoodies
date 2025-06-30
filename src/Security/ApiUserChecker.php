<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiUserChecker implements UserCheckerInterface
{

    public function __construct(private RequestStack $requestStack) {}
    // AVANT l’authentification : ne rien faire ici
    public function checkPreAuth(UserInterface $user): void
    {
        // Vide
    }

    // APRÈS authentification : blocage si accès API désactivé
    public function checkPostAuth(UserInterface $user): void
{
    if (!$user instanceof User) {
        return;
    }

    $request = $this->requestStack->getCurrentRequest();

    // On bloque SEULEMENT lors du login API
    if ($request && $request->getPathInfo() === '/api/login_check') {
        if (!$user->isApiAccessEnabled()) {
            throw new CustomUserMessageAuthenticationException('403::Accès API non activé. Activez-le dans votre profil.');
        }
    }
}
}
