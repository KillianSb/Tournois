<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentType;
use App\Repository\TeamRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/tournois')]
class TournamentController extends AbstractController
{

    public function __construct(
        public HttpClientInterface $client
    ) {
    }
    #[Route('/', name: 'tournois_home', methods: ['GET'])]
    public function home(TournamentRepository $tournamentRepository): Response
    {
        return $this->render('tournament/home.html.twig', [
            'tournaments' => $tournamentRepository->findAllTournament(),
        ]);
    }

    #[Route('/nouveau', name: 'tournois_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client
    ): Response
    {
        /*
        //Requête de l'API La Poste pour récupérer les adresses

        $response = $client->request('GET', 'https://api-adresse.data.gouv.fr/search/?q=8+bd+du+port');

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        //$data = file_get_contents('data.json');
        $obj = json_decode($content);
        foreach ($obj->features as $feature) {
            //echo $feature->properties->label . "<br>";
            $result[] = $feature->properties->label;
        }
        */
        //dd($result);

        $tournament = new Tournament();
        //Récupération du statut pour éviter la modification dans le formulaire
        //$status = $tournament->getStatus();

        //Récupération de la date de création pour éviter la modification dans le formulaire
        //$dateCreation = $tournament->getDateCreation();

        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        $user = $this->getUser();
        $tournament->setUser($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournament);
            $entityManager->flush();

            return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/nouveau.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
            //'status' => $status,
            //'dateCreation' => $dateCreation,
        ]);
    }

    #[Route('/{id}', name: 'tournois_infos', methods: ['GET'])]
    public function infos(
        Tournament $tournament,
        TeamRepository $teamRepository)
    : Response
    {
        $teams = $teamRepository->findAll();

        // Mélange des équipes
        // TODO : A mettre en base de donnée pour stocker 1 fois (nouvelle entité ?)
        shuffle($teams);

        return $this->render('tournament/infos.html.twig', [
            'tournament' => $tournament,
            'teams' => $teams,
        ]);
    }

    #[Route(
        '/{id}/modifier',
        name: 'tournois_modifier',
        methods: ['GET', 'POST']
    )]
    public function modifier(Request $request, Tournament $tournament, EntityManagerInterface $entityManager): Response
    {
        $dateCreation = $tournament->getDateCreation();
        $status = $tournament->getStatus();

        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/modifier.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
            'dateCreation' => $dateCreation,
            'status' => $status
        ]);
    }

    #[Route('/{id}', name: 'tournois_supprimer', methods: ['POST'])]
    public function supprimer(Request $request, Tournament $tournament, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
    }
}
