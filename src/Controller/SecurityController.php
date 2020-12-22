<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/{id}/lo
     * gout", name="app_logout")
     */
    public function logout(User $user,UserRepository $userRepository)
    {
        return $this->render("security/login.html.twig");

    }
    /**
     * @Route("/recovery", name="recover_password", methods={"GET","POST"})
     */
    public function recovery(Request $request,UserRepository $userRepository,\Swift_Mailer $mailer): Response
    {
        $mail = $request->request->get('email');
        $res=$userRepository->findPartenaires('%"ROLE_ADMIN"%');
        $admin=$res[0];
        $message = (new \Swift_Message('Coordonnées :'))
            ->setFrom($mail)
            ->setSubject("Récuperation mot de passe")
            ->setTo($admin->getEmail())
            ->setBody("Demande de changement/récuperation mot de passe pour cette adresse : $mail");
        $mailer->send($message);
        return new JsonResponse(['statut' =>"load success hamdi"], JsonResponse::HTTP_OK);
    }
}
