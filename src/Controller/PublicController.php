<?php

namespace App\Controller;

use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'public_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les tournois depuis le gestionnaire d'entités
        $tournamentRepository = $entityManager->getRepository(Tournament::class);
        $tournaments = $tournamentRepository->findAll();

        // Récupérer les images des jeux liés à tous les tournois
        $gameImages = [];
        // Récupérer le nombre d'équipes inscrites pour chaque tournoi
        $teamCounts = [];
        foreach ($tournaments as $tournament) {
            // Si le tournoi a un jeu associé, récupérer l'image du jeu
            if ($tournament->getVideogame() !== null) {
                $gameImages[] = $tournament->getVideogame()->getPicture();
                $teamCounts[$tournament->getId()] = count($tournament->getTeam());
            }
        }

        return $this->render('public/home.html.twig', [
            'tournaments' => $tournaments,
            'gameImages' => $gameImages,
            'teamCounts' => $teamCounts
        ]);
    }

    #[Route('/a-propos-de-nous', name: 'public_aboutus')]
    public function aboutus(
    ): Response
    {
        return $this->render('public/aboutus.html.twig', [

        ]);
    }
}

