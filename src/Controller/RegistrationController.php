<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/enregistrer', name: 'registration_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer)
    : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $signedUrl = $signatureComponents->getSignedUrl();
            $this->sendVerificationEmail($mailer, $user, $signedUrl);
            $this->addFlash('success', sprintf(
                'La confirmation de ton email vient d\'être envoyer vers  : %s',
                $user->getEmail()
            ));

            return $this->redirectToRoute('public_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/verification_email', name: 'verify_email')]
    public function verifyEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('registration_register');
        }
        $user->setIsActive(true);
        $entityManager->flush();
        $this->addFlash('success', 'Compte vérifié !');
        $user->setIsActive(1);
        return $this->redirectToRoute('security_login');
    }


    #[Route(path: '/verify/resend', name: 'registration_resend_email')]
    public function resendVerifyEmail(): Response
    {
        return $this->render('registration/resend_verify_email.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendVerificationEmail(MailerInterface $mailer, User $user, string $signedUrl): void
    {
        $email = (new Email())
            ->from('support@tournamentsmaster.fr')
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Vérification de votre adresse email -TOURNAMENTS MASTER')
            ->text('Bonjour, Afin de vérifier ton adresse mail, click sur le lien suivant :')
            ->html(sprintf('<a href="%s">%s</a>', $signedUrl, $signedUrl));
        $mailer->send($email);
    }
}
