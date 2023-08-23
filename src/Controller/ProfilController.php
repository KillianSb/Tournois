<?php

namespace App\Controller;

use App\Entity\User;
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
    ): Response
    {
        $user = $this->getUser();
        $infosProfil = $userRepository->find($user);
        return $this->render('profil/infos.html.twig', compact('infosProfil'));
    }
}
