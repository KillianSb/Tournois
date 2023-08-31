<?php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Form\VideoGameType;
use App\Repository\TournamentRepository;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/jeu')]
class VideoGameController extends AbstractController
{
    #[Route('x', name: 'video_game_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(VideoGameRepository $videoGameRepository): Response
    {
        return $this->render('video_game/index.html.twig', [
            'video_games' => $videoGameRepository->findAllVideoGames(),
        ]);
    }

    #[Route('/nouveau', name: 'video_game_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $videoGame = new VideoGame();
        $form = $this->createForm(VideoGameType::class, $videoGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($videoGame);
            $entityManager->flush();

            return $this->redirectToRoute('video_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video_game/new.html.twig', [
            'video_game' => $videoGame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'video_game_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(
        VideoGame $videoGame,
        TournamentRepository $tournamentRepository
    ): Response
    {
        $tournaments = $tournamentRepository->findBy(['videogame' => $videoGame]);

        return $this->render('video_game/show.html.twig',
            compact(
                'tournaments',
                'videoGame'
            )
        );
    }

    #[Route('/{id}/modifier', name: 'video_game_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoGameType::class, $videoGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('video_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video_game/modifier.html.twig', [
            'video_game' => $videoGame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'video_game_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$videoGame->getId(), $request->request->get('_token'))) {
            $entityManager->remove($videoGame);
            $entityManager->flush();
        }

        return $this->redirectToRoute('video_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
