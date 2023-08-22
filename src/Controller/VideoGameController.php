<?php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Form\VideoGameType;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jeu')]
class VideoGameController extends AbstractController
{
    #[Route('x', name: 'video_game_index', methods: ['GET'])]
    public function index(VideoGameRepository $videoGameRepository): Response
    {
        return $this->render('video_game/index.html.twig', [
            'video_games' => $videoGameRepository->findAllVideoGames(),
        ]);
    }

    #[Route('/nouveau', name: 'video_game_new', methods: ['GET', 'POST'])]
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
    public function show(VideoGame $videoGame): Response
    {
        return $this->render('video_game/show.html.twig', [
            'video_game' => $videoGame,
        ]);
    }

    #[Route('/{id}/modifier', name: 'video_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoGameType::class, $videoGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('video_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video_game/edit.html.twig', [
            'video_game' => $videoGame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'video_game_delete', methods: ['POST'])]
    public function delete(Request $request, VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$videoGame->getId(), $request->request->get('_token'))) {
            $entityManager->remove($videoGame);
            $entityManager->flush();
        }

        return $this->redirectToRoute('video_game_index', [], Response::HTTP_SEE_OTHER);
    }
}