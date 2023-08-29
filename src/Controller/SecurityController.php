<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{

    #[Route(path: '/connection', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('public_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Get the error message stored in the session flash
        $inactiveAccountError = $session->get('inactive');

        return $this->render('security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'inactive_account_error' => $inactiveAccountError
            ]
        );
    }


    #[Route(path: '/deconnexion', name: 'security_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut être vide - elle sera interceptée par la clé de déconnexion de votre pare-feu.');
    }
}
