<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\User;
use App\Form\User2Type;
use App\Form\UserType;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET","POST"})
     */
    public function index(Request $request,UserRepository $userRepository): Response
    {
        if($request->isXmlHttpRequest()) {
            $id =  json_decode($request->request->get('id'),true);
            $id1 =  json_decode($request->request->get('id1'),true);
            $r1=$userRepository->findBy(['id'=>$id1]);
            $r3=$userRepository->findBy(['id'=>$id]);
            $r2=null;
            foreach ( $r1 as $item){
                $r2=$item;
            }
            $r4=null;
            foreach ( $r3 as $value){
                $r4=$value;
            }
            $r2->setLevel($r4);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse($result=$r4->getUsername());
        }
        $role=null;
        $user=$userRepository->findAll();
        $part=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
        $comm=$userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%');
        $prosp=$userRepository->findAllPartenaires('%"ROLE_PROSPECT"%');
        return $this->render('/Admin/user/index.html.twig', [
            'msg'=>'not ok',
            'users' => $user,
            'role'=>$role,
            'part'=>$part,
            'comm'=>$comm,
            'prosp'=>$prosp,
        ]);
    }
    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder,UserRepository $userRepository): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_COMMERCIALE"]);
         $form = $this->createForm(User2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $nom = $form['email']->getData();
            $id = 0;
            $services = $entityManager->getRepository(User::class)->findBy(['email' => $nom]);
            foreach ($services as $row) {
                if ($row->getEmail() == $nom) {
                    $id = 1;
                } else {
                   $id=0;
                }
            }
            if ($id == 1) {
                return $this->render('/Admin/user/new.html.twig', [
                    'user' => $user,
                    'erreur' => null,
                    'form' => $form->createView(),
                ]);
            } else {
                $user->setPassword(
                    $encoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $contact = $form->getData();
                $message = (new \Swift_Message('Coordonnées :'))
                    ->setFrom("hamdi.ghribi.98.99@gmail.com")
                    ->setSubject("Idantifiants de compte 'REQUEST QUOTE'")
                    ->setTo($form->get('email')->getData())
                    ->setBody('<h4>Vous pouvez maintenant acceder a votre compte grace à ces identifiants :</h4><br>-Nom d utilisateur :' . $form->get('username')->getData() . '<br>-Mot de passe : ' . $form->get('password')->getData() , 'text/html');
                $mailer->send($message);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $role = $form['roles']->getData();
                $this->getDoctrine()->getManager()->flush();
                $role=null;
                $user=$userRepository->findAll();
                $part=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
                $comm=$userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%');
                $prosp=$userRepository->findAllPartenaires('%"ROLE_PROSPECT"%');
                return $this->render('/Admin/user/index.html.twig', [
                    'msg'=>'okk',
                    'users' => $user,
                    'role'=>$role,
                    'part'=>$part,
                    'comm'=>$comm,
                    'prosp'=>$prosp,
                ]);
            }
        }

        return $this->render('/Admin/user/new.html.twig', [
            'user' => $user,
            'erreur'=>'aa',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user,UserRepository $userRepository,DemandeRepository $demandeRepository): Response
    {
        if ($user->getRoles()[0] == "ROLE_PARTENAIRE"){
        $r1=count($userRepository->findBy(['level'=>$user->getId()]));
        $r2=count($userRepository->findPartenaires('%"ROLE_COMMERCIALE"%'));
        $r4=count($demandeRepository->findAll());
        $r3=count($demandeRepository->findBy(['Partenaire'=>$user->getId()]));
        $r5=0;
        }
        elseif($user->getRoles()[0]=="ROLE_COMMERCIALE"){
            $r3=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En attente']));
            $r4=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En cours de création']));
            $r5=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'Validée']));
            $r1=count($demandeRepository->findBy(['Commerciale'=>$user->getId()]));
            $r2=count($demandeRepository->findBy(['Partenaire'=>$user->getLevel()]));
        }
        else{
            $r3=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En attente']));
            $r4=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En cours de création']));
            $r5=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'Validée']));
            $r1=count($demandeRepository->findBy(['Commerciale'=>$user->getId()]));
            $r2=count($demandeRepository->findBy(['Partenaire'=>$user->getLevel()]));
        }
        return $this->render('/Admin/user/show.html.twig', [
            'user' => $user,
            'r1'=>$r1,
            'r2'=>$r2,
            'r3'=>$r3,
            'r4'=>$r4,
            'r5'=>$r5,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user,UserRepository $userRepository, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder): Response
    {
        $id=$request->get('id');
        $user1=$userRepository->findById($id);
        $roles='';
        $form = $this->createForm(UserType::class, $user);
            foreach ($user1 as $item){
                $roles=$item->getRoles();
            }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $contact = $form->getData();
            $message = (new \Swift_Message('Coordonnées :'))
                ->setFrom("hamdi.ghribi.98.99@gmail.com")
                ->setSubject("Modification sur le compte")
                ->setTo($form->get('email')->getData())
                ->setBody('<h2>Cher Monsieur,Madame<h3><br><h4>Quelques informations ont été changer dans votre compte</h4><br>Vous pouvez maintenat accéder a votre espace :<br>-Nom d utilisateur : ' . $form->get('username')->getData() . '<br>-Mot de passe : '. $form->get('password')->getData() , 'text/html');
            $mailer->send($message);
            $this->getDoctrine()->getManager()->flush();
            $role=null;
            $user=$userRepository->findAll();
            $part=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
            $comm=$userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%');
            $prosp=$userRepository->findAllPartenaires('%"ROLE_PROSPECT"%');
            return $this->render('/Admin/user/index.html.twig', [
                'msg'=>'okk',
                'users' => $user,
                'role'=>$role,
                'part'=>$part,
                'comm'=>$comm,
                'prosp'=>$prosp,
            ]);
        }

        return $this->render('/Admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user,EntityManagerInterface $em,UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user->getRoles()==["ROLE_PROSPECT"]){
                $demande =new Demande();
                $demandeRepository=$em->getRepository(Demande::class);
                $res=$demandeRepository->findBy(['Prospect'=>$user->getId()]);
                $res2=null;
                foreach ($res as $item){
                    $item->setPartenaire(null);
                    $item->setCommerciale(null);
                    $item->setService(null);
                    $this->getDoctrine()->getManager()->flush();
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }elseif($user->getRoles()==["ROLE_PARTENAIRE"])
            {
                $demande =new Demande();
                $demandeRepository=$em->getRepository(Demande::class);
                $res=$demandeRepository->findBy(['Partenaire'=>$user->getId()]);
                $res2=null;
                foreach ($res as $item){
                    $item->setPartenaire(null);
                    $this->getDoctrine()->getManager()->flush();
                    $res2=$item;
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }elseif($user->getRoles()==["ROLE_COMMERCIALE"]){
                $demande =new Demande();
                $demandeRepository=$em->getRepository(Demande::class);
                $res=$demandeRepository->findBy(['Commerciale'=>$user->getId()]);
                $res2=null;
                foreach ($res as $item){
                    $item->setCommerciale(null);
                    $this->getDoctrine()->getManager()->flush();
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }else{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }
        $role=null;
        $user=$userRepository->findAll();
        $part=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
        $comm=$userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%');
        $prosp=$userRepository->findAllPartenaires('%"ROLE_PROSPECT"%');
        return $this->render('/Admin/user/index.html.twig', [
            'msg'=>'okk',
            'users' => $user,
            'role'=>$role,
            'part'=>$part,
            'comm'=>$comm,
            'prosp'=>$prosp,
        ]);
    }

    /**
     * @Route("commercial", name="commercial_id")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function commercial(Request $request,UserRepository $userRepository)
    {
        if($request->isXmlHttpRequest()) {
            $result=null;
            return new JsonResponse($result);
        }
    }
    /**
     * @Route("/{id}/editAccount", name="user_Accountedit", methods={"GET","POST"})
     */
    public function editAccount(Request $request, User $user,UserRepository $userRepository, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();
            $role=null;
            $user=$userRepository->findAll();
            $part=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
            $comm=$userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%');
            $prosp=$userRepository->findAllPartenaires('%"ROLE_PROSPECT"%');
            return $this->render('/Admin/user/index.html.twig', [
                'msg'=>'okk',
                'users' => $user,
                'role'=>$role,
                'part'=>$part,
                'comm'=>$comm,
                'prosp'=>$prosp,
            ]);
        }

        return $this->render('Admin/user/editAccount.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/connected", name="user_connected", methods={"GET","POST"})
     */
    public function isConnected(Request $request, User $user,UserRepository $userRepository): Response
    {
        $id = $request->request->get('idUser');
        $res=$userRepository->findOneBy(['id'=>$id]);
        $res->setConnecte(true)
            ->setLastLogin(new \DateTime());
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['statut' =>"load success hamdi"], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}/disconnected", name="user_disconnected", methods={"GET","POST"})
     */
    public function disConnected(Request $request, User $user,UserRepository $userRepository): Response
    {
        $res=$userRepository->findOneBy(['id'=>$user->getId()]);
        $res->setConnecte(0);
        $this->getDoctrine()->getManager()->flush();
         return $this->redirectToRoute("app_logout",[
            'id'=>$user->getId()
        ]);
    }
}
