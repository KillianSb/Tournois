<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Form\TournamentType;
use App\Repository\TeamRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    public function infos(Tournament $tournament): Response
    {
        $teams = $tournament->getTeam();
        //dd($teams->count());

        return $this->render('tournament/infos.html.twig', [
            'tournament' => $tournament,
            'teams' => $teams
        ]);
    }

    #[Route('/{id}/rejoindre', name: 'tournois_join', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function rejoindre(
        Tournament $tournament,
        TeamRepository $teamRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('security_login', [], Response::HTTP_SEE_OTHER);
        }

        $team = $request->query->get('equipes');
        if ($team != null) {
            $team = $teamRepository->find($team);
            $tournament->addTeam($team); //Ajout d'un étournament
            $entityManager->persist($tournament);
            $entityManager->flush();
        }
        dump($team);

        if ($user->getTeams()->count() >= $tournament->getNbTeamMax()) {
            return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('tournament/rejoindre.html.twig', [
            'tournament' => $tournament,
            'team' => $team,
            'user' => $user
        ]);
    }


    #[Route(
        '/{id}/rejoindre/test',
        name: 'tournois_join_team',
        requirements: ['id' => '\d+'],
        methods: ['POST', 'GET']
    )]
    public function rejoindreTournament(
        Request $request,
        int $id,
    ): Response
    {

        if ($this->getUser() == null) {
            return $this->redirectToRoute('security_login', [], Response::HTTP_SEE_OTHER);
        }

            /*
                $tournament->addTeam($team);
                $entityManager->persist($tournament);
                $entityManager->flush();
            */
        return $this->render('tournament/test.html.twig', [
            'id' => $id
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
