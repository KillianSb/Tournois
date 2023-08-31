<?php

namespace App\Controller;



use App\Entity\Game;
use App\Entity\Result;
use App\Entity\TableTeamTournament;
use App\Entity\Tournament;
use App\Form\GameType;
use App\Form\ResultType;
use App\Form\TournamentType;
use App\Repository\ResultRepository;
use App\Repository\TableTeamTournamentRepository;
use App\Repository\TeamRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        $tournaments = $tournamentRepository->findAllTournament();

        return $this->render('tournament/home.html.twig', [
            'tournaments' => $tournaments
        ]);
    }

    #[Route('/nouveau', name: 'tournois_nouveau', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function nouveau(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $tournament = new Tournament();

        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        $user = $this->getUser();
        $tournament->setUser($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addOrganizerRole();
            $entityManager->persist($user);
            $entityManager->persist($tournament);
            $entityManager->flush();

            return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/nouveau.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tournois_infos', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function infos(
        Tournament $tournament,
        TeamRepository $teamRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        ResultRepository $resultRepository,
        TableTeamTournamentRepository $tableTeamTournamentRepository
    ): Response
    {
        $teams = $teamRepository->findTeamsByTournament($tournament->getId());

        // Vérifier si le tableau d'équipes mélangées existe déjà pour ce tournoi
        if ($tournament->getTableTeamTournament() == null) {
            // Mélange des équipes
            shuffle($teams);

            // Créer un tableau d'IDs d'équipes mélangées
            $shuffledTeamIds = array_map(function ($teams) {
                return $teams->getId();
            }, $teams);

            $tableTeamTournament = new TableTeamTournament();
            $tableTeamTournament->setShuffleTableTeam($shuffledTeamIds);

            // Associer le tableau d'équipes mélangées au tournoi
            $tournament->setTableTeamTournament($tableTeamTournament);

            $entityManager->persist($tableTeamTournament);
            $entityManager->flush();

        } else {
            // Si le tableau est déjà créé, récupérez les IDs mélangés et récupérez les équipes correspondantes
            $shuffledTeamIds = $tournament->getTableTeamTournament()->getShuffleTableTeam();
            $shuffledTeams = [];
            foreach ($shuffledTeamIds as $teamsId) {
                $shuffledTeams[] = $teamRepository->find($teamsId);
            }
            $teams = $shuffledTeams;
        }

    //Création formulaire partie
        $result = new Result();
        $form = $this->createForm(ResultType::class, $result);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*dd($result);*/
            $entityManager->persist($result);
            $entityManager->flush();
        }

        $result = $resultRepository->findAll();
        $resultTournament = $tableTeamTournamentRepository->findAll();


        // Récupérer les IDs des équipes gagnantes
        $teamWinnerIds = [];
        foreach ($result as $resultItem) {
            $teamWinner = $resultItem->getTeamWinner();
            if ($teamWinner) {
                $teamWinnerIds[] = $teamWinner->getId();
            }
        }/*dd($teamWinnerIds);*/

        // Trouver les emplacements des équipes gagnantes dans le tableau ShuffleTableTeam
        $teamPositions = [];
        foreach ($resultTournament as $tableTeamTournament) {
            $shuffledTeamIds = $tableTeamTournament->getShuffleTableTeam();
            foreach ($shuffledTeamIds as $position => $teamId) {
                if (in_array($teamId, $teamWinnerIds)) {
                    $teamPositions[$teamId] = $position;
                }
            }
        }/*dd($teamPositions);*/

        // Récupérer le chemin de l'image du jeu associé
        $gameImage = $tournament->getVideogame()->getPicture();

        return $this->render('tournament/infos.html.twig', [
            'tournament' => $tournament,
            'teams' => $teams,
            'gameImage' => $gameImage,
            'form' => $form,
            'teamPositions' => $teamPositions
        ]);
    }

    #[Route('/{id}/rejoindre', name: 'tournois_join', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ORGANIZER')]
    public function rejoindre(
        Tournament $tournament,
        TeamRepository $teamRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('security_login', [], Response::HTTP_SEE_OTHER);
        }

        $teamId = $request->query->get('equipes');
        if ($teamId != null) {
            $team = $teamRepository->find($teamId);

            // Vérifier si les joueurs de l'équipe sont déjà inscrits dans d'autres équipes du tournoi
            $players = $team->getUser();
            foreach ($tournament->getTeam() as $otherTeam) {
                if ($otherTeam->getId() !== $team->getId()) {
                    foreach ($otherTeam->getUser() as $otherPlayer) {
                        if ($players->contains($otherPlayer)) {
                            // Afficher un message d'erreur et empêcher l'inscription
                            $session->getFlashBag()->add('error', 'Un joueur de cette équipe est déjà inscrit dans une autre équipe du tournoi.');
                            return $this->redirectToRoute('tournois_infos', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
                        }
                    }
                }
            }

            // Si aucune duplication n'est trouvée, inscrivez l'équipe au tournoi
            $tournament->addTeam($team);
            $entityManager->persist($tournament);
            $entityManager->flush();

            $session->getFlashBag()->add('success', 'L\'équipe a été inscrite avec succès au tournoi.');
            return $this->redirectToRoute('tournois_infos', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/rejoindre.html.twig', [
            'tournament' => $tournament,
            'team' => $teamId,
            'user' => $user
        ]);
    }

    #[Route(
        '/{id}/modifier',
        name: 'tournois_modifier',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ORGANIZER')]
    public function modifier(Request $request, Tournament $tournament, EntityManagerInterface $entityManager, Security $security): Response
    {
        $dateCreation = $tournament->getDateCreation();
        $status = $tournament->getStatus();

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID du tournoi
        if ($user && $user->getId() === $tournament->getUser()->getId() || $this->isGranted('ROLE_SUPER_ADMIN', $user) || $this->isGranted('ROLE_ADMIN', $user)){
            // Vérifier si l'utilisateur a le rôle nécessaire
            if ($this->isGranted('ROLE_ORGANIZER', $user) || $this->isGranted('ROLE_ADMIN', $user)) {
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
            } else {
                // L'utilisateur n'a pas les droits nécessaires
                return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'tournois_supprimer', methods: ['POST'])]
    #[IsGranted('ROLE_ORGANIZER')]
    public function supprimer(Request $request, Tournament $tournament, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID du tournoi
        if ($user && $user->getId() === $tournament->getUser()->getId() || $this->isGranted('ROLE_SUPER_ADMIN', $user) || $this->isGranted('ROLE_ADMIN', $user)) {
            // Vérifier si l'utilisateur a le rôle nécessaire
            if ($this->isGranted('ROLE_ORGANIZER', $user)) {
                if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
                    $entityManager->remove($tournament);
                    $entityManager->flush();
                } else {
                    // Le jeton CSRF n'est pas valide, vous pouvez gérer cela en lançant une exception appropriée
                    throw new \Exception('Jeton CSRF invalide.');
                }

                return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
            } else {
                // L'utilisateur n'a pas les droits nécessaires
                /*throw $this->createAccessDeniedException();*/
                return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }
}
