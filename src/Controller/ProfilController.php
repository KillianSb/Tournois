<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_infos', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function infos(
        UserRepository $userRepository,
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
