<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTFailureListener
{
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();
        $message = $exception->getMessage();

        // Cas spécial : accès API désactivé
        if (str_starts_with($message, '403::')) {
            $cleanMessage = str_replace('403::', '', $message);

            $response = new JsonResponse([
                'code' => 403,
                'error' => 'Accès interdit',
                'message' => $cleanMessage,
            ], 403);

            $event->setResponse($response);
            return;
        }

        // Cas par défaut : mauvais identifiants
        $response = new JsonResponse([
            'code' => 401,
            'message' => 'Identifiants incorrects.'
        ], 401);

        $event->setResponse($response);
    }
}
