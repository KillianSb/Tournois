<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_infos', methods: ['GET'])]
    public function infos(
        UserRepository $userRepository,
    ): Response
    {
        $user = $this->getUser();
        $infosProfil = $userRepository->find($user);
        return $this->render('profil/infos.html.twig', compact('infosProfil'));
    }
}
