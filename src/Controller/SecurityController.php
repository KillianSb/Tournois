<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityController extends AbstractController
{
    #[Route(path: '/connection', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('public_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérification de isActive
        if ($error === null) {
            $user = $security->getUser();
            if ($user !== null && $user->isIsActive() === 0) {
                $error = new \Symfony\Component\Security\Core\Exception\BadCredentialsException('Votre compte est désactivé !');
            }
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'security_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
