<?php

namespace App\Controller;

use App\Entity\Avancement;
use App\Form\AvancementType;
use App\Repository\AvancementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/avancement")
 */
class AvancementController extends AbstractController
{
    /**
     * @Route("/", name="avancement_index", methods={"GET"})
     */
    public function index(AvancementRepository $avancementRepository): Response
    {
        return $this->render('avancement/index.html.twig', [
            'avancements' => $avancementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="avancement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $avancement = new Avancement();
        $form = $this->createForm(AvancementType::class, $avancement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avancement);
            $entityManager->flush();

            return $this->redirectToRoute('avancement_index');
        }

        return $this->render('avancement/new.html.twig', [
            'avancement' => $avancement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="avancement_show", methods={"GET"})
     */
    public function show(Avancement $avancement): Response
    {
        return $this->render('avancement/show.html.twig', [
            'avancement' => $avancement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="avancement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Avancement $avancement): Response
    {
        $form = $this->createForm(AvancementType::class, $avancement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('avancement_index');
        }

        return $this->render('avancement/edit.html.twig', [
            'avancement' => $avancement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="avancement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Avancement $avancement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avancement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($avancement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('avancement_index');
    }
}
