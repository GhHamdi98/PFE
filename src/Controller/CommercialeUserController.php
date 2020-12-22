<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommercialeUserController extends AbstractController
{
    /**
     * @Route("/commerciale/user", name="commerciale_user")
     */
    public function index(UserRepository $UserRepository,DemandeRepository $demandeRepository)
    {
        $users = $this->getUser();
        $user=$UserRepository->findOneBy(['username'=>$users->getUsername()]);
        $partenaire=$user->getLevel();
        $commerciale=$UserRepository->findBy(['level'=>$user->getId()]);
        $res=$demandeRepository->findBy(['Commerciale'=>$user->getId()]);
        $prospects=array();
        $j=0;
        $i=0;
        foreach ($res as $item){
            if ($item->getProspect()->getDemandeProspect()){
                $i++;
            }
            if (in_array($item->getProspect(),$prospects))
            {
                $j=1;
            }else
            {
                array_push($prospects,$item->getProspect());
            }
        }
        return $this->render('commerciales/user/index.html.twig', [
            'user'=>$partenaire,
            'commerciale'=>$user,
            'prospects'=>$prospects,
        ]);
    }
    /**
     * @Route("/commerciale/{id}", name="commerciale_show", methods={"GET"})
     */
    public function show(User $user,UserRepository $userRepository,DemandeRepository $demandeRepository): Response
    {
        $commerciale=$userRepository->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $r0=0;
        if ($user->getRoles()[0] == "ROLE_PARTENAIRE"){
            $r3=count($demandeRepository->findBy(['Partenaire'=>$user->getId(),'Etat'=>'En attente']));
            $r4=count($demandeRepository->findBy(['Partenaire'=>$user->getId(),'Etat'=>'En cours de création']));
            $r5=$r3+$r4;
            $r1=count($demandeRepository->findBy(['Commerciale'=>$commerciale->getId(),'Etat'=>'En attente']));
            $r2=count($demandeRepository->findBy(['Commerciale'=>$commerciale->getId(),'Etat'=>'En cours de création']));
            $r0=$r1+$r2;
            $r1=count($userRepository->findAllPartenaires('%"ROLE_COMMERCIALE"%'));
            $r2=count($userRepository->findBy(['level'=>$user->getId()]));
        }
        elseif($user->getRoles()[0]=="ROLE_COMMERCIALE"){
            $r3=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En attente']));
            $r4=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En cours de création']));
            $r5=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'Validée']));
            $r1=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'Validée']));
            $r2=count($demandeRepository->findBy(['Commerciale'=>$user->getId()]));
        }
        else{
            $r3=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En attente']));
            $r4=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'En cours de création']));
            $r5=count($demandeRepository->findBy(['Commerciale'=>$user->getId(),'Etat'=>'Validée']));
            $r1=count($demandeRepository->findBy(['Commerciale'=>$user->getId()]));
            $r2=count($demandeRepository->findBy(['Partenaire'=>$user->getLevel()]));
        }
        return $this->render('/commerciales/user/show.html.twig', [
            'user' => $user,
            'r1'=>$r1,
            'r2'=>$r2,
            'r3'=>$r3,
            'r4'=>$r4,
            'r5'=>$r5,
            'r0'=>$r0,
        ]);
    }
    /**
     * @Route("/commerciale/{id}/connected", name="commerciale_connected", methods={"GET","POST"})
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
     * @Route("commerciale/{id}/disconnected", name="commerciale_disconnected", methods={"GET","POST"})
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
    /**
     * @Route("commerciale/{id}/editAccount", name="commercialeUser_Accountedit", methods={"GET","POST"})
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
            $name=$form->get('username')->getData();
            $email=$form->get('email')->getData();
            $pays=$form->get('pays')->getData();
            $adresse=$form->get('adresse')->getData();
            $activite=$form->get('activite')->getData();
            $telephone=$form->get('telephone')->getData();
            $user->setUsername($name)
                ->setEmail($email)
                ->setPays($pays)
                ->setAdresse($adresse)
                ->setActivite($activite)
                ->setTelephone($telephone);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commerciale_user');
        }

        return $this->render('commerciales/user/editAccount.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
