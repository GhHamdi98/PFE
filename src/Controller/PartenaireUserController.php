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

class PartenaireUserController extends AbstractController
{
    /**
     * @Route("/partenaire/user", name="partenaire_user")
     */
    public function index(UserRepository $UserRepository,DemandeRepository $demandeRepository)
    {
        $users = $this->getUser();
        $user=$UserRepository->findOneBy(['username'=>$users->getUsername()]);
        $commerciale=$UserRepository->findBy(['level'=>$user->getId()]);
        $res=$demandeRepository->findBy(['Partenaire'=>$user->getId()]);
        $prospects=array();
        $i=0;
        foreach ($res as $item){
            if ($item->getProspect()->getDemandeProspect()){
                $i++;
            }
            array_push($prospects,$item->getProspect());
        }

        return $this->render('partenaire/user/index.html.twig', [
            'user'=>$users,
            'commerciale'=>$commerciale,
            'prospects'=>$prospects,
            'compteur'=>$i,
            'msg'=>'not ok',
        ]);
    }
    /**
     * @Route("/partenaire/{id}", name="partenaire_show", methods={"GET"})
     */
    public function show(User $user,UserRepository $userRepository,DemandeRepository $demandeRepository): Response
    {
        if ($user->getRoles()[0] == "ROLE_PARTENAIRE"){
            $r1=count($userRepository->findBy(['level'=>$user->getId()]));
            $r2=count($userRepository->findPartenaires('%"ROLE_COMMERCIALE"%'));
            $r3=0;$r4=0;$r5=0;
            $count=$demandeRepository->findBy(['Partenaire'=>$user->getId()]);
            foreach ($count as $item){
                if ($item->getEtat()=="En cours de création"){
                    $r4=$r4+1;
                }elseif ($item->getEtat()=="En attente"){
                    $r3=$r3+1;
                }else{
                    $r5=$r5+1;
                }
            }
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
        return $this->render('/partenaire/user/show.html.twig', [
            'user' => $user,
            'r1'=>$r1,
            'r2'=>$r2,
            'r3'=>$r3,
            'r4'=>$r4,
            'r5'=>$r5,
        ]);
    }
    /**
     * @Route("/partenaire/{id}/connected", name="partenaire_connected", methods={"GET","POST"})
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
     * @Route("partenire/{id}/disconnected", name="partenaire_disconnected", methods={"GET","POST"})
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
     * @Route("partenaire/{id}/editAccount", name="partenaireUser_Accountedit", methods={"GET","POST"})
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

            return $this->redirectToRoute('partenaire_user',[
                'msg'=>'ok',
            ]);
        }

        return $this->render('partenaire/user/editAccount.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
