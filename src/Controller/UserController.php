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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/utilisateur')]
class UserController extends AbstractController
{

    #[Route('/{id}/modifier', name: 'utilisateur_modifier', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function modifier(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $currentUser = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID passé en paramètre
        if ($currentUser && $currentUser->getId() === $user->getId() || $this->isGranted('ROLE_SUPER_ADMIN', $user) || $this->isGranted('ROLE_ADMIN', $user)) {
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
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/supprimer/{id}', name: 'utilisateur_supprimer', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function supprimer(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('public_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desactiver/{id}', name: 'utilisateur_desactiver', methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function desactiverUtilisateur(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l utilisateur connecté
        $currentUser = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID passé en paramètre
        if ($currentUser && $currentUser->getId() === $user->getId() || $this->isGranted('ROLE_SUPER_ADMIN', $user) || $this->isGranted('ROLE_ADMIN', $user)) {
            // Désactiver l'utilisateur actuel
            $currentUser->setIsActive(false);
            $entityManager->flush();

            // Déconnexion de l'utilisateur actuel
            return $this->redirectToRoute('security_logout', [], Response::HTTP_SEE_OTHER);
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }
}
