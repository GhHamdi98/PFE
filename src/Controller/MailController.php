<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\User;
use App\Form\MailType;
use App\Repository\MailRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("/", name="mail_index", methods={"GET"})
     */
    public function index(MailRepository $mailRepository): Response
    {
        return $this->render('mail/index.html.twig', [
            'mails' => $mailRepository->findAll(),
            'msg'=>'not okk',
        ]);
    }

    /**
     * @Route("/new", name="mail_new", methods={"GET","POST"})
     */
    public function new(Request $request,MailRepository $mailRepository): Response
    {
        $mail = new Mail();
        $form = $this->createForm(MailType::class, $mail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mail);
            $entityManager->flush();

            return $this->render('mail/index.html.twig', [
                'mails' => $mailRepository->findAll(),
                'msg'=>'okk',
            ]);
        }

        return $this->render('mail/new.html.twig', [
            'mail' => $mail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mail_show", methods={"GET"})
     */
    public function show(Mail $mail): Response
    {
        return $this->render('mail/show.html.twig', [
            'mail' => $mail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mail_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mail $mail,MailRepository $mailRepository): Response
    {
        $form = $this->createForm(MailType::class, $mail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('mail/index.html.twig', [
                'mails' => $mailRepository->findAll(),
                'msg'=>'okk',
            ]);
        }

        return $this->render('mail/edit.html.twig', [
            'mail' => $mail,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/send/mail", name="send_mails", methods={"GET","POST"})
     */
    public function send(Request $request,EntityManagerInterface $em): Response
    {
        $mailRepository =$em->getRepository(Mail::class);;
        $userRepository =$em->getRepository(User::class);
        $user=$userRepository->findAll();
        $partenaire=array();
        $commerciale=array();
        $prospect=array();
        $administrateur=array();
        foreach ($user as $item){
            if ($item->getRoles()[0]=="ROLE_PARTENAIRE"){
                array_push($partenaire,$item);
            }elseif ($item->getRoles()[0]=="ROLE_COMMERCIALE"){
                array_push($commerciale,$item);
            }elseif($item->getRoles()[0]=="ROLE_PROSPECT"){
                array_push($prospect,$item);
            }else{
                array_push($administrateur,$item);
            }
        }
        return $this->render('mail/send.html.twig',
            [
            'mails'=>$mailRepository->findAll(),
            'partenaires'=>$partenaire,
            'commerciales'=>$commerciale,
            'prospects'=>$prospect,
      ]);
    }
    /**
     * @Route("sendit", name="sendf_mails", methods={"GET","POST"})
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
    /**
     * @Route("/{id}", name="mail_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mail $mail,MailRepository $mailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mail);
            $entityManager->flush();
        }

        return $this->render('mail/index.html.twig', [
            'mails' => $mailRepository->findAll(),
            'msg'=>'okk',
        ]);
    }
}
