<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\PaysRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemandeProspectController extends AbstractController
{
    /**
     * @Route("/demande/prospect", name="demande_prospect")
     */
    public function new(Request $request,UserRepository $userRepository,PaysRepository $repository,ServiceRepository $serviceRepository,DemandeRepository $demandeRepository): Response
    {
        $demande = new Demande();
        $service=new Service();
        $res8=$serviceRepository->findAll();
        $userss=new User();
        $pay=$repository->findAll();
        $user=$userRepository->findPartenaires('%"ROLE_PARTENAIRE"%');
        if($request->isMethod('post')){
            $nature_projet = $request->request->get("nature_projet");
            if ($nature_projet !== null) {
                $fonctionalite_projet = $request->request->get("fonctionalite_projet");
                $refonte = $request->request->get("refonte");
                $budget=$request->request->get("budget");
                $serv=$serviceRepository->findOneBy(['nomService'=>'Refonte']);
                $module_b2c = $request->request->get("module_b2c");
                $module_b2b = $request->request->get("module_b2b");
                $langue_projet = $request->request->get("langue_projet");
                $couleur_prefere = $request->request->get("couleur_prefere");
                $logo = $request->request->get("logo");
                $charte_graphique = $request->request->get("charte_graphique");
                $site_similaire = $request->request->get("site_similaire");
                $echeance = $request->request->get("echeance");
                $details = $request->request->get("details");
                $services = $request->request->get("service");
                $ser=$serviceRepository->findBy(['id'=>$services]);
                $res11=null;
                foreach($ser as $item11)
                {
                    $res11=$item11;
                }
                $partenaire = $userRepository->findById($request->request->get("partenaire"));
                $res1=null;
                foreach($partenaire as $item)
                {
                    $res1=$item;
                }
                $commerciale =  $userRepository->findById($request->request->get("commerciale"));
                $res2=null;
                foreach($commerciale as $item1)
                {
                    $res2=$item1;
                }
                $demande->setNatureProjet($nature_projet)
                    ->setRefonte($refonte)
                    ->setPrincipaleProjet($refonte)
                    ->setModuleB2c($module_b2c)
                    ->setModuleB2b($module_b2b)
                    ->setLangueProjet($langue_projet)
                    ->setCouleurPrefere($couleur_prefere)
                    ->setLogo($logo)
                    ->setBudget($budget)
                    ->setCharteGraphique($charte_graphique)
                    ->setSiteSimilaire($site_similaire)
                    ->setEcheance($echeance)
                    ->setDetails($details)
                    ->setPartenaire($res1)
                    ->setDateCreation(new DateTime());
                    if($res11==null){
                        $demande->setService($serv);
                    }else
                    {
                        $demande->setService($res11);
                    }
                    $demande->setEtat("En attente")
                    ->setCommerciale($res2);
                $username = $request->request->get("username");
                $password = $request->request->get("password");
                $email = $request->request->get("email");
                $pays =$repository->findById($request->request->get("pays"));
                $res3=null;
                foreach($pays as $item2)
                {
                    $res3=$item2;
                }
                $adresse = $request->request->get("adresse");
                $activite = $request->request->get("activite");
                $telephone = $request->request->get("telephone");
                $userss->setUsername($username)
                    ->setPassword($password)
                    ->setEmail($email)
                    ->setPays($res3)
                    ->setAdresse($adresse)
                    ->setActivite($activite)
                    ->setTelephone($telephone)
                    ->setRoles(['ROLE_PROSPECT']);
                $res4=$userRepository->findemail($email);
                $res5=null;
                foreach($res4 as $item3)
                {
                    $res5=$item3;
                }
                if ($res4 == null){
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($userss);
                    $demande->setProspect($userss);
                    $entityManager->persist($demande);
                    $entityManager->flush();
                    return $this->render('/demande/new.html.twig', [
                        'demande' => $demande,
                        'user'=>$user,
                        'msg'=>'ok',
                        'pay'=>$pay,
                        'service'=>$res8,]);
                }else
                {
                    $entityManager = $this->getDoctrine()->getManager();
                    $demande->setProspect($res5);
                    $entityManager->persist($demande);
                    $entityManager->flush();
                    $demande=$demandeRepository->findAll();
                    return $this->render('/demande/new.html.twig', [
                        'msg'=>'ok',
                        'demande' => $demande,
                        'user'=>$user,
                        'pay'=>$pay,
                        'service'=>$res8,
                    ]);
                }


            }
        }
        return $this->render('/demande/new.html.twig', [
            'demande' => $demande,
            'user'=>$user,
            'pay'=>$pay,
            'msg'=>'not ok',
            'service'=>$res8,
        ]);
    }
    /**
     * @Route("/demande/prospect/service", name="demande_prospect_service")
     */
    public function services(Request $request,ServiceRepository $serviceRepository): Response
    {
        if($request->isXmlHttpRequest()) {
            $result=$serviceRepository->findAll();
            return new JsonResponse($result);
        }
    }
}
