<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/utilisateur')]
class UserController extends AbstractController
{

    #[Route('/{id}/modifier', name: 'utilisateur_modifier', methods: ['GET', 'POST'])]
    public function modifier(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profil_infos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/modifier.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('supprimer/{id}', name: 'utilisateur_supprimer', methods: ['POST'])]
    public function supprimer(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('public_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('desactiver/{id}', name: 'utilisateur_desactiver', methods: ['GET','POST'])]
    public function desactiverUtilisateur(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {

        // Désactiver l'utilisateur actuel
        $security->getUser()->setIsActive(0);
        $entityManager->flush();
        // Déconnexion de l'utilisateur actuel

        return $this->redirectToRoute('security_logout', [], Response::HTTP_SEE_OTHER);

    }
}
