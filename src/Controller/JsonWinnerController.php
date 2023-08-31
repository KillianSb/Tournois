<?php

namespace App\Controller;

use App\Entity\Result;
use App\Repository\ResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

class JsonWinnerController extends AbstractController
{
    #[Route('/get_winner_data', name: 'json_winner')]
    public function json_winner(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ResultRepository $repository
    ): JsonResponse
    {
        $winnerData = $repository->findAll();

        // You might need to adjust the properties to match your Entity's structure
        $data = [
//            'nb_partie' => $winnerData->getNbPartie(),
//            'equipe_position' => $winnerData->getTeamWinner(),
            // Add more properties if needed
            'winners' => $winnerData
        ];

        return $this->json($winnerData,
            200,
            ['groups' => ['teamWinner']]);
    }
}
