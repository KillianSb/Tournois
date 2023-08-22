<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/public', name: 'public_home')]
    public function home(): Response
    {
        return $this->render('public/home.html.twig', [
            'controller_name' => 'PublicController',
        ]);
    }
}
