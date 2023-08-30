<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'public_home')]
    public function home(
    ): Response
    {
        return $this->render('public/home.html.twig', [
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

