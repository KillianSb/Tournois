<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/equipe')]
class TeamController extends AbstractController
{

    // Controleur de la liste des équipes *************************************************************************
    #[Route(
        '/',
        name: '_liste',
        methods: ['GET']
    )]
    #[IsGranted('ROLE_USER')]
    public function liste(
        TeamRepository $teamRepository
    ): Response
    {
        return $this->render('equipe/_liste.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }


    // Controleur de l'ajout d'une équipe ***************************************************************************
    #[Route(
        '/enregistrement',
        name: '_enregistrement',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function enregistrement(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $team->setLeaderId($user->getId());
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('_liste', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('equipe/enregistrement.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }


    // Controleur de l'affichage d'une équipe ************************************************************************
    #[Route(
        '/detail{id}',
        name: '_detail',
        methods: ['GET']
    )]
    #[IsGranted('ROLE_USER')]
    public function detail(
        Team $team
    ): Response
    {;
        $user = $this->getUser()->getId();
        $listUser = $team->getUser();
        $leaderId = $team->getLeaderId();
        //dd($listUser);

        return $this->render('equipe/_detail.html.twig', [
            'team' => $team,
            'userId' => $user,
            'leaderId' => $leaderId
        ]);
    }

    #[Route(
        '/detail{id}/rejoindre',
        name: 'rejoindre',
        methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function joinTeam(
        Team $team,
        EntityManagerInterface $entityManager
    ): Response
    {;
        $user = $this->getUser();

        $team->addUser($user);
        $entityManager->persist($team);
        $entityManager->flush();

        return $this->render(
            'equipe/rejoindre.html.twig',
            compact('user', 'team')
        );
    }

    // Controleur de l'edition d'une équipe **************************************************************************
    #[Route(
        '/{id}/modification',
        name: '_modification',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    // TODO Mettre un champ "chef d'"équipe" pour faire la vérification
    public function modification(
        Request $request,
        Team $team,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('_liste', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/modification.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }


    // Controleur de la suppression d'une équipe ***********************************************************************
    #[Route(
        '/supprimer{id}',
        name: '_supprimer',
        methods: ['POST'    ]
    )]
    #[IsGranted('ROLE_USER')]
    // TODO Mettre un champ "chef d'"équipe" pour faire la vérification
    public function supprimer(
        Request $request,
        Team $team,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            '_liste', [], Response::HTTP_SEE_OTHER);
    }
}
