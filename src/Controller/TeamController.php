<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Services\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/equipe')]
class TeamController extends AbstractController
{

    // Controleur de la liste des équipes *************************************************************************
    #[Route(
        '/liste',
        name: '_liste',
        methods: ['GET'])]
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
        methods: ['GET', 'POST'])]
    public function enregistrement(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('_liste', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('equipe/enregistrement.html.twig', [
            'equipe' => $team,
            'form' => $form,
        ]);
    }


    // Controleur de l'affichage d'une équipe ************************************************************************
    #[Route(
        '/detail{id}',
        name: '_detail',
        methods: ['GET'])]
    public function detail(
        Team $team
    ): Response
    {
        return $this->render('equipe/_detail.html.twig', [
            'equipe' => $team,
        ]);
    }

    // Controleur de l'edition d'une équipe **************************************************************************
    #[Route(
        '/{id}/modification',
        name: '_modification',
        methods: ['GET', 'POST'])]
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
            'equipe' => $team,
            'form' => $form,
        ]);
    }


    // Controleur de la suppression d'une équipe ***********************************************************************
    #[Route(
        '/supprimer{id}',
        name: '_supprimer',
        methods: ['POST']
    )]
    public function supprimer(
        Request $request,
        Team $team,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('_liste', [], Response::HTTP_SEE_OTHER);
    }
}
