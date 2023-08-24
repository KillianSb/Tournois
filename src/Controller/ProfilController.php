<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_infos', methods: ['GET'])]
    public function infos(
        UserRepository $userRepository,
        TeamRepository $teamRepository
    ): Response
    {
        $user = $this->getUser();
        $infosProfil = $userRepository->find($user);

        $teams = $user->getTeams();

        // Tableau pour stocker les tournois
        $tournaments = [];

        foreach ($teams as $team) {
            // Récupérer les tournois associés à chaque équipe
            $teamTournaments = $team->getTournaments();

            // Parcourir les tournois
            foreach ($teamTournaments as $tournament) {
                // Récupérer le jeu associé à ce tournoi
                $videoGame = $tournament->getVideogame();

                // Vérifier si le jeu est lié et a une image
                if ($videoGame !== null) {
                    $gamePicture = $videoGame->getPicture();

                    // Stocker l'image du jeu dans le tableau des tournois
                    $tournament->gamePicture = $gamePicture;
                }
            }
            // Fusionner les tournois dans le tableau global
            $tournaments = array_merge($tournaments, $teamTournaments->toArray());
        }

        return $this->render('profil/infos.html.twig', compact('infosProfil', 'teams', 'tournaments'));
    }
}
