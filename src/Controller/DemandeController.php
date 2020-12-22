<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\DemandeContrat;
use App\Entity\Devis;
use App\Entity\DevisOptions;
use App\Entity\DevisService;
use App\Form\Demande1Type;
use App\Form\Service2Type;
use App\Repository\AvancementRepository;
use App\Repository\DemandeContratRepository;
use App\Repository\DemandeRepository;
use App\Repository\DevisRepository;
use App\Repository\DevisServiceRepository;
use App\Repository\OptionRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demande")
 */
class DemandeController extends AbstractController
{
    /**
     * @Route("/", name="demande_index", methods={"GET"})
     */
    public function index(DemandeRepository $demandeRepository,AvancementRepository $avancementRepository): Response
    {
        return $this->render('demande/index.html.twig', [
            'demande' => $demandeRepository->findAll(),
            'avancements'=>$avancementRepository->findAll(),
            'msg'=>'not ok',
        ]);
    }

    /**
     * @Route("/{id}/Devis", name="demande_show3", methods={"GET","POST"})
     */
    public function show3(Request $request,DevisRepository $devisRepository,DevisServiceRepository $devisServiceRepository,DemandeContratRepository $demandeContratRepository,Demande $demande,OptionRepository $optionRepository,AvancementRepository $avancementRepository): Response
    {

        $tarifT=$demande->getService()->getPrix();
        $options='';
        foreach ($demande->getService()->getOptions() as $item){
            $options=$options."<br>".$item;
            $tarifT=$tarifT+$item->getPrix();
        }
        $ch1=$demande->getService()->getContrat();
        $ch1 = str_replace (  "[username]" ,$demande->getProspect()->getUsername() , $ch1);
        $ch1 = str_replace (  "[pays]" ,$demande->getService()->getPays() , $ch1);
        $ch1 = str_replace (  "[email]" ,$demande->getProspect()->getEmail() , $ch1);
        $ch1 = str_replace (  "[Service]" ,$demande->getService()->getNomService() , $ch1);
        $ch1 = str_replace (  "[Options]" ,$options , $ch1);
        $ch1 = str_replace (  "[Tarif]" ,$tarifT , $ch1);
        $form = $this->createForm(Service2Type::class, $demande->getService());
        $form->handleRequest($request);
        $options=$optionsByService=$optionRepository->findBy(['service'=>$demande->getService()->getId()]);
        $options=$optionsByService=$optionRepository->findBy(['service'=>$demande->getService()->getId()]);
        $stat=$avancementRepository->findBy(['demande'=>$demande->getId()]);
        $statCount=count($avancementRepository->findBy(['demande'=>$demande->getId()]));
        $devis=$devisRepository->findOneBy(['demande'=>$demande->getId()]);
        $res8=$demandeContratRepository->findOneBy(['demande'=>$demande->getId()]);
        return $this->render('demande/show3.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
            'options'=>$options,
            'contrat'=>$ch1,
             'stat'=>$stat,
             'statCount'=>$statCount,
            'devis'=>$devis,
            'nouveauContrat'=>$res8,
            'services'=>$devisServiceRepository->findAll(),
        ]);

    }

    /**
     * @Route("/{id}/edit", name="demande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,DemandeRepository $demandeRepository, Demande $demande,DemandeContratRepository $demandeContratRepository,DevisRepository $devisRepository,UserRepository $userRepository): Response
    {
            $form = $this->createForm(Demande1Type::class, $demande);
            $form->handleRequest($request);
            $ContratDemande = new DemandeContrat();
            $devis = new Devis();
            $partenaires=$userRepository->findAllPartenaires('%"ROLE_PARTENAIRE"%');
            $res = $demandeContratRepository->findOneBy(['demande' => $demande->getId()]);
            $res1 = $devisRepository->findOneBy(['demande' => $demande->getId()]);
            if ($form->isSubmitted() && $form->isValid())
            {
                $etatDevis = $_POST("devis");
                $res1->setStatut($etatDevis);
                $this->getDoctrine()->getManager()->flush();
                $etatContrat = $_POST("contrat");
                $res->setEtat($etatContrat);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('demande_index');
            }

            return $this->render('demande/edit.html.twig', [
                'demande' => $demande,
                'contrat' => $res,
                'devis' => $res1,
                'partenaires' => $partenaires,
            ]);
        }
    /**
     * @Route("/{id}/editf", name="demande_editf", methods={"GET","POST"})
     */
    public function editf(Request $request,DemandeRepository $demandeRepository,AvancementRepository $avancementRepository ,Demande $demande,DemandeContratRepository $demandeContratRepository,DevisRepository $devisRepository,\Swift_Mailer $mailer,UserRepository $userRepository): Response
    {
        $res = $demandeContratRepository->findOneBy(['demande' => $demande->getId()]);
        $res1 = $devisRepository->findOneBy(['demande' => $demande->getId()]);
            $partenaire=$request->request->get("partenaire");
            $user=$userRepository->findOneBy(['id'=>$partenaire]);
            $demande->setPartenaire($user);
            $this->getDoctrine()->getManager()->flush();
            if ($user != null){
            $message = (new \Swift_Message('Coordonnées :'))
                ->setFrom("hamdi.ghribi.98.99@gmail.com")
                ->setSubject("Affectation sur projet")
                ->setTo($user->getEmail())
                ->setBody('Cher Monsieur,vous etes affecté à un projet... Accéder à votre compte pour plus dinformations');
            $mailer->send($message);
            }
            $etatDevis = $request->request->get("devis");
            $res1->setStatut($etatDevis)
            ->setDateConfirmation(new DateTime());
            $this->getDoctrine()->getManager()->flush();
            $etatContrat = $request->request->get("contrat");
            if ($demandeContratRepository->findOneBy(['demande'=>$demande->getId()]) != null)
                $res->setEtat($etatContrat);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('demande/index.html.twig',[
                'msg'=>'okk',
                'demande' => $demandeRepository->findAll(),
            'avancements'=>$avancementRepository->findAll(),
            ]);
    }
    /**
     * @Route("/{id}", name="demande_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Demande $demande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $demande->setPartenaire(null)
                ->setCommerciale(null)
                ->setService(null)
                ->setProspect(null);
            $this->getDoctrine()->getManager()->flush();
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('demande_index');
    }
    /**
     * @Route("demande_comm", name="commercialcomm_id")
     */
    public function commercial(Request $request,UserRepository $userRepository)
    {
        if($request->isXmlHttpRequest()) {
            $var =  json_decode($request->request->get('id'),true);
            $user=$userRepository->findByLevel($var);
            $serializer = SerializerBuilder::create()->build();

            $data = $serializer->serialize($user, 'json');
            return new JsonResponse(['status' => $data], JsonResponse::HTTP_OK);
        }
        else{
            return new JsonResponse(['code'=>200,'message'=>'mal jouée','data'=>null]);
        }
    }
    /**
     * @Route("devis", name="devis")
     */
    public function devis(Request $request,UserRepository $userRepository,DemandeRepository $demandeRepository,EntityManagerInterface $em)
    {
        $devis=new Devis();
        $service=new DevisService();
        if($request->isXmlHttpRequest()) {
            $devisOptions=new DevisOptions();
            $infos = $request->request->get('infoss');
            $infos=trim($infos,'"');
            $tab1=$_POST['tab'];
            $tab2=array($tab1);
            $length =$_POST['length'];
            $number=(int)$length;
            $reduction = json_decode($request->request->get('reduction'), true);
            $servicePrix = json_decode($request->request->get('servicePrix'), true);
            $serviceNom = json_encode($request->request->get('serviceNom'), true);
            $serviceNom=trim($serviceNom,'"');
            $totaleFinal =json_decode($request->request->get('tataleFinal'), true);
            $demandeF = json_decode($request->request->get('demandeF'), true);
            $demande =$demandeRepository->findOneBy(['id'=>((int)($demandeF))]);
            $devis->setDateCreation(new DateTime())
                ->setDemande($demande)
                ->setInformations($infos)
                ->setTotale((float)$totaleFinal)
                ->setReduction((float)$reduction)
                ->setDateConfirmation(null)
                ->setStatut("En attente");
            $repository = $em->getRepository(Devis::class);
            $res1=$repository->findOneBy(['demande'=>((int)($demandeF))]);
            if ($res1== null) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($devis);
                $entityManager->flush();
                $res2=$repository->findOneBy(['demande'=>((int)($demandeF))]);
                $service->setPrix((int)$servicePrix)
                    ->setNom($serviceNom)
                    ->setDevis($res2);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();
            for ($j = 0; $j < $number; $j++) {
                    $id = $tab2[0][$j];
                    $devisOptions = new DevisOptions();
                    $devisOptions->setPrix((float)$id['prix']);
                    $devisOptions->setNom((string)$id['nom']);
                    $devisOptions->setDevis($res2);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($devisOptions);
                    $entityManager->flush();
                }
            }
            else{
                $drepository = $em->getRepository(Devis::class);
                $res2=$drepository->findOneBy(['demande'=>((int)($demandeF))]);
                $id=$res2->getId();
                $dserviceRepository = $em->getRepository(DevisService::class);
                $devisRes=$dserviceRepository->findOneBy(['devis'=>((int)$id)]);
                $optionRepository = $em->getRepository(DevisOptions::class);
                $DevisOptions=$optionRepository->findBy(['devis'=>((int)$id)]);
                if (count($DevisOptions)>0){
                    foreach ($DevisOptions as $row){
                        $em->remove($row);
                        $em->flush();
                    }
                }
                $em->remove($devisRes);
                $em->flush();
                $em->remove($res2);
                $em->flush();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($devis);
                $entityManager->flush();
                $res2=$repository->findOneBy(['demande'=>((int)($demandeF))]);
                $service->setPrix((float)$servicePrix)
                        ->setNom($serviceNom)
                        ->setDevis($res2);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($service);
                $entityManager->flush();
                for ($j = 0; $j < $number; $j++) {
                    $devisOptions = new DevisOptions();
                    $id = $tab2[0][$j];
                    $devisOptions->setPrix((float)$id['prix']);
                    $devisOptions->setNom((string)$id['nom']);
                    $devisOptions->setDevis($res2);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($devisOptions);
                    $entityManager->flush();
                }
            }
            return new JsonResponse(['statut' =>"aa"], JsonResponse::HTTP_OK);
        }
        else{
            return new JsonResponse(['code'=>200,'message'=>'mal jouée','data'=>null]);
        }
    }
    /**
     * @Route("/{id}/devisPDF",name="devis_showPDF")
     */
    public function showDevis(Demande $demande,EntityManagerInterface $em): Response
    {
        $devis =new Devis();
        $devisService=new DevisService();
        $devisSRepository=$em->getRepository(DevisService::class);
        $devisRepository=$em->getRepository(Devis::class);
        $id=$demande->getId();
        $res=$devisRepository->findOneBy(['demande'=>$id]);
        $res1=$devisSRepository->findOneBy(['devis'=>$res->getId()]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $pdfOptions->set('isRemoteEnabled',true);
        $dompdf = new Dompdf($pdfOptions);
        $page= $this->render('demande/showDevis.html.twig', [
            'demande' => $demande,
            'devis'=>$res,
            'service'=>$res1,
        ])->getContent();
        $dompdf->loadHtml($page);
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream('result.pdf',[
           'Attachment'=>false,
       ]);
    }

    /**
     * @Route("/{id}/sendDevis",name="devis_send")
     * @param Demande $demande
     * @param EntityManagerInterface $em
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function sendDevis(Request $request,Demande $demande,EntityManagerInterface $em, \Swift_Mailer $mailer): Response
    {
        $sujet = $request->request->get('sujet');
        $corps = $request->request->get('corps');
        $devis =new Devis();
        $devisService=new DevisService();
        $devisSRepository=$em->getRepository(DevisService::class);
        $devisRepository=$em->getRepository(Devis::class);
        $id=$demande->getId();
        $res=$devisRepository->findOneBy(['demande'=>$id]);
        $res1=$devisSRepository->findOneBy(['devis'=>$res->getId()]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $pdfOptions->set('isRemoteEnabled',true);
        $dompdf = new Dompdf($pdfOptions);
        $page= $this->render('demande/showDevis.html.twig', [
            'demande' => $demande,
            'devis'=>$res,
            'service'=>$res1,
        ])->getContent();
        $dompdf->loadHtml($page);
        $dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
        $dompdf->render();
        $file=$dompdf->output();
        $nom="devis".$devis->getId().".pdf";
        file_put_contents($nom, $file);
        $message = (new \Swift_Message('Coordonnées :'))
            ->setFrom("hamdi.ghribi.98.99@gmail.com")
            ->setTo($demande->getProspect()->getEmail())
            ->setSubject($sujet)
            ->setBody($corps,'text/html')
            ->attach(\Swift_Attachment::fromPath($nom));
        $mailer->send($message);
        return new JsonResponse(['statut' =>"aa"], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}/contrat",name="enregistrer_contrat")
     */
    public function saveContrat(Request $request,Demande $demande,EntityManagerInterface $em): Response
    {
        $contrat = $request->request->get('contrat');
        $nouveauContrat=new DemandeContrat();
        $nouveauContrat->setContrat($contrat)
                       ->setDemande($demande)
                       ->setEtat('En attente');
        $contratRepository=$em->getRepository(DemandeContrat::class);
        $res=$contratRepository->findOneBy(['demande'=>$demande->getId()]);
        if ($res == null)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nouveauContrat);
            $entityManager->flush();
        }
        else{
            $em->remove($res);
            $em->flush();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nouveauContrat);
            $entityManager->flush();
        }
        return new JsonResponse(['statut' =>$contrat], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}/contratPDF",name="contrat_showPDF")
     */
    public function showContrat(Demande $demande,EntityManagerInterface $em): Response
    {
        $demandeContrat=$em->getRepository(DemandeContrat::class);
        $id=$demande->getId();
        $res=$demandeContrat->findOneBy(['demande'=>$id]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $pdfOptions->set('isRemoteEnabled',true);
        $dompdf = new Dompdf($pdfOptions);
        $page= $this->render('demande/showContrat.html.twig', [
            'demande' => $demande,
            'contrat'=>$res->getContrat()
        ])->getContent();
        $dompdf->loadHtml($page);
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream('contrat.pdf',[
            'Attachment'=>false,
        ]);
    }
    /**
     * @Route("/{id}/sendContrat",name="contrat_send")
     * @param Demande $demande
     * @param EntityManagerInterface $em
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function sendContrat(Request $request,Demande $demande,EntityManagerInterface $em, \Swift_Mailer $mailer): Response
    {
        $sujet = $request->request->get('sujet');
        $corps = $request->request->get('corps');
        $demandeContrat=$em->getRepository(DemandeContrat::class);
        $id=$demande->getId();
        $res=$demandeContrat->findOneBy(['demande'=>$id]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $pdfOptions->set('isRemoteEnabled',true);
        $dompdf = new Dompdf($pdfOptions);
        $page= $this->render('demande/showContrat.html.twig', [
            'demande' => $demande,
            'contrat'=>$res->getContrat(),
        ])->getContent();
        $dompdf->loadHtml($page);
        $dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
        $dompdf->render();
        $file=$dompdf->output();
        $nom="contrat.pdf";
        file_put_contents($nom, $file);
        $message = (new \Swift_Message('Coordonnées :'))
            ->setFrom("hamdi.ghribi.98.99@gmail.com")
            ->setTo($demande->getProspect()->getEmail())
            ->setSubject($sujet)
            ->setBody($corps,'text/html')
            ->attach(\Swift_Attachment::fromPath($nom));
        $mailer->send($message);
        return new JsonResponse(['statut' =>"aa"], JsonResponse::HTTP_OK);
    }
}
