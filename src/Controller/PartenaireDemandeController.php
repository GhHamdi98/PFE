<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\DemandeContrat;
use App\Entity\Devis;
use App\Entity\DevisService;
use App\Entity\User;
use App\Form\Service2Type;
use App\Repository\AvancementRepository;
use App\Repository\DemandeContratRepository;
use App\Repository\DemandeRepository;
use App\Repository\DevisRepository;
use App\Repository\DevisServiceRepository;
use App\Repository\OptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/partenaire/demande")
 */
class PartenaireDemandeController extends AbstractController
{
    /**
     * @Route("/", name="partenaireDemande_index", methods={"GET"})
     * @param DemandeRepository $demandeRepository
     * @return Response
     */
    public function index(DemandeRepository $demandeRepository,EntityManagerInterface $em,AvancementRepository $avancementRepository): Response
    {
        $avan=$avancementRepository->findAll();
        $UserRepository=$em->getRepository(User::class);
        $users = $this->getUser();
        $user=$UserRepository->findOneBy(['username'=>$users->getUsername()]);
        $commerciales=$UserRepository->findBy(['level'=>$user->getId()]);
        $demandes =$demandeRepository->findBy(['Partenaire'=>$user->getId()]);
        return $this->render('partenaire/demande/index.html.twig', [
            'demandes' => $demandes,
            'commerciales'=>$commerciales,
            'avancements'=>$avan,
        ]);
    }
    /**
     * @Route("/{id}/Devis", name="partenaireDemande_index_show3", methods={"GET","POST"})
     */
    public function show3(Request $request,DevisRepository $devisRepository,DevisServiceRepository $devisService,DemandeContratRepository $demandeContratRepository,Demande $demande,OptionRepository $optionRepository,AvancementRepository $avancementRepository): Response
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
        $service=$devisService->findOneBy(['devis'=>$devis->getId()]);
        return $this->render('partenaire/demande/show3.html.twig', [
            'demande' => $demande,
            'service'=>$service,
            'form' => $form->createView(),
            'options'=>$options,
            'contrat'=>$ch1,
            'stat'=>$stat,
            'statCount'=>$statCount,
            'devis'=>$devis,
            'nouveauContrat'=>$res8,
        ]);

    }
    /**
     * @Route("/{id}/devisPDF",name="partenaireDevis_showPDF")
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
        $page= $this->render('partenaire/demande/showDevis.html.twig', [
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
     * @Route("/{id}/contratPDF",name="partenaireContrat_showPDF")
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
        $page= $this->render('partenaire/demande/showContrat.html.twig', [
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
     * @Route("/edit", name="partenairedemande_edit")
     */
    public function edit(Request $request,DemandeRepository $demandeRepository,UserRepository $userRepository, \Swift_Mailer $mailer): Response
    {
        $demandeId =  json_decode($request->request->get('id'),true);
        $demande=$demandeRepository->findOneBy(['id'=>$demandeId]);
        $commercialId =  json_decode($request->request->get('comm'),true);
        $commerciale=$userRepository->findOneBy(['id'=>$commercialId]);
        $demande->setCommerciale($commerciale);
        $this->getDoctrine()->getManager()->flush();
        $message = (new \Swift_Message('Coordonnées :'))
            ->setFrom($demande->getPartenaire()->getEmail())
            ->setSubject("Affectation sur projet")
            ->setTo($commerciale->getEmail())
            ->setBody('Un nouveau projet est affecté à vous . Acceder à votre espace pour plus d information');
        $mailer->send($message);
        return new JsonResponse($commerciale->getUsername());
    }
}