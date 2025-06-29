<?php

namespace App\EventListener;

use App\Exception\CustomApiAccessDeniedException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTFailureListener
{
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        // Cas spécial : accès API non activé
        if ($exception instanceof CustomApiAccessDeniedException) {
            $response = new JsonResponse([
                'code' => 403,
                'error' => 'Accès interdit',
                'message' => $exception->getMessageKey(),
            ], 403);

            $event->setResponse($response);
            return;
        }

        // Cas par défaut : identifiants incorrects
        $response = new JsonResponse([
            'code' => 401,
            'message' => 'Identifiants incorrects.'
        ], 401);

        $event->setResponse($response);
    }
}
