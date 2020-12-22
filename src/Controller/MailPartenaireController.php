<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Mail;
use App\Entity\User;
use App\Repository\MailRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailPartenaireController extends AbstractController
{
    /**
     * @Route("/partenaire/send/mail", name="Partenairesend_mails", methods={"GET","POST"})
     */
    public function send(Request $request,EntityManagerInterface $em): Response
    {
        $mailRepository =$em->getRepository(Mail::class);;
        $userRepository =$em->getRepository(User::class);
        $demandeRepository =$em->getRepository(Demande::class);
        $partenaire=$userRepository->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $commercials=$userRepository->findBy(['level'=>$partenaire->getId()]);
        $demandes=$demandeRepository->findBy(['Partenaire'=>$partenaire]);
        $prospects=array();
        $array=$userRepository->findAll();
        $administrateur=array();
        foreach ($array as $value)
        {
            if($value->getRoles()[0]=="ROLE_ADMIN"){
                array_push($administrateur,$value);
            }
        }
        foreach ($demandes as $item)
        {
            array_push($prospects,$item->getProspect());
        }
        return $this->render('partenaire/mail/send.html.twig',
            [
                'mails'=>$mailRepository->findAll(),
                'administrateur'=>$administrateur,
                'commerciales'=>$commercials,
                'prospects'=>$prospects,
            ]);
    }
    /**
     * @Route("sendit", name="Partenairesendf_mails", methods={"GET","POST"})
     */
    public function sendf(Request $request,EntityManagerInterface $em,UserRepository $userRepository,MailRepository $mailRepository,\Swift_Mailer $mailer): Response
    {
        $user = $request->request->get('user');
        $dist=$userRepository->findOneBy(['id'=>$user]);
        $from=$userRepository->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $mail = $request->request->get('mail');
        $mailsended=$mailRepository->findOneBy(['id'=>$mail]);
        $message = (new \Swift_Message('Coordonnées :'))
            ->setFrom($from->getEmail())
            ->setSubject($mailsended->getSujet())
            ->setTo($dist->getEmail())
            ->setBody($mailsended->getCorps(), 'text/html');
        $mailer->send($message);
        return new JsonResponse("ok");
    }
}
