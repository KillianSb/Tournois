<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentType;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/tournois')]
class TournamentController extends AbstractController
{

    public function __construct(
        public HttpClientInterface $client
    ) {
    }
    #[Route('/', name: 'tournois_home', methods: ['GET'])]
    public function home(TournamentRepository $tournamentRepository): Response
    {
        return $this->render('tournament/home.html.twig', [
            'tournaments' => $tournamentRepository->findAllTournament(),
        ]);
    }

    #[Route('/nouveau', name: 'tournois_nouveau', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function nouveau(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client
    ): Response
    {
        /*
        //Requête de l'API La Poste pour récupérer les adresses

        $response = $client->request('GET', 'https://api-adresse.data.gouv.fr/search/?q=8+bd+du+port');

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        //$data = file_get_contents('data.json');
        $obj = json_decode($content);
        foreach ($obj->features as $feature) {
            //echo $feature->properties->label . "<br>";
            $result[] = $feature->properties->label;
        }
        */
        //dd($result);

        $tournament = new Tournament();
        //Récupération du statut pour éviter la modification dans le formulaire
        //$status = $tournament->getStatus();

        //Récupération de la date de création pour éviter la modification dans le formulaire
        //$dateCreation = $tournament->getDateCreation();

        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        $user = $this->getUser();
        $tournament->setUser($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_ORGANIZER"]);
            $entityManager->persist($tournament);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/nouveau.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
            //'status' => $status,
            //'dateCreation' => $dateCreation,
        ]);
    }

    #[Route('/{id}', name: 'tournois_infos', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function infos(Tournament $tournament): Response
    {
        return $this->render('tournament/infos.html.twig', [
            'tournament' => $tournament,
        ]);
    }

    #[Route(
        '/{id}/modifier',
        name: 'tournois_modifier',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ORGANIZER')]
    public function modifier(Request $request, Tournament $tournament, EntityManagerInterface $entityManager, Security $security): Response
    {
        $dateCreation = $tournament->getDateCreation();
        $status = $tournament->getStatus();

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID du tournoi
        if ($user && $user->getId() === $tournament->getUser()->getId()){
            // Vérifier si l'utilisateur a le rôle nécessaire
            if ($this->isGranted('ROLE_ORGANIZER', $user) || $this->isGranted('ROLE_ADMIN', $user)) {
                $form = $this->createForm(TournamentType::class, $tournament);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager->flush();

                    return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
                }

                return $this->render('tournament/modifier.html.twig', [
                    'tournament' => $tournament,
                    'form' => $form,
                    'dateCreation' => $dateCreation,
                    'status' => $status
                ]);
            } else {
                // L'utilisateur n'a pas les droits nécessaires
                /*throw $this->createAccessDeniedException();*/
                return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'tournois_supprimer', methods: ['POST'])]
    #[IsGranted('ROLE_ORGANIZER')]
    public function supprimer(Request $request, Tournament $tournament, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est connecté et son ID correspond à l'ID du tournoi
        if ($user && $user->getId() === $tournament->getUser()->getId()) {
            // Vérifier si l'utilisateur a le rôle nécessaire
            if ($this->isGranted('ROLE_ORGANIZER', $user)) {
                if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
                    $entityManager->remove($tournament);
                    $entityManager->flush();
                } else {
                    // Le jeton CSRF n'est pas valide, vous pouvez gérer cela en lançant une exception appropriée
                    throw new \Exception('Jeton CSRF invalide.');
                }

                return $this->redirectToRoute('tournois_home', [], Response::HTTP_SEE_OTHER);
            } else {
                // L'utilisateur n'a pas les droits nécessaires
                /*throw $this->createAccessDeniedException();*/
                return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            // L'utilisateur n'est pas autorisé à accéder à cette page
            /*throw $this->createAccessDeniedException();*/
            return $this->redirectToRoute('error_403', [], Response::HTTP_SEE_OTHER);
        }
    }
}
