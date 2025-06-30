<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterTypeForm;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response {
        $user = new User();

        $form = $this->createForm(RegisterTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérifie que l'email n’est pas déjà utilisé
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée.'));
            } else {
                // Hashage et sauvegarde
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }
        }

        return $this->render('register/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
