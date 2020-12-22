<?php

namespace App\Controller;

use App\Entity\Option;
use App\Entity\Service;
use App\Form\Service1Type;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/service")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("/", name="service_index", methods={"GET"})
     */
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'msg'=>'not ok',
        ]);
    }

    /**
     * @Route("/new", name="service_new", methods={"GET","POST"})
     */
    public function new(Request $request,ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $option =new Option();
        $service->addOption($option);
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $nom =$form['nomService']->getData();
            $id=0;
            $services=$entityManager->getRepository(Service::class)->findBy(['nomService'=>$nom]);
            foreach ($services as $row) {
                if ($row->getNomService() == $nom) {
                    $id=1;
                } else {
                    $id=$row->getId();
                    $service = $entityManager->getRepository(Service::class)->find($id);
                }
            }
            if ($id==1) {
                return $this->render('service/new.html.twig', [
                    'service' => $service,
                    'erreur' => null,
                    'form' => $form->createView(),
                ]);
            } else {
                $entityManager->persist($service);
                $entityManager->flush();
                $options = $entityManager->getRepository(Option::class)->findBy(['service'=>null]);
                foreach ($options as $item) {
                    $item->setService($service);
                    $entityManager->flush();

                }

                return $this->render('service/index.html.twig', [
                    'services' => $serviceRepository->findAll(),
                    'msg'=>'okk',
                ]);
            }
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'erreur'=>'fsqc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_show", methods={"GET"})
     */
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Service $service,ServiceRepository $serviceRepository): Response
    {
        $form = $this->createForm(Service1Type::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('service/index.html.twig', [
                'services' => $serviceRepository->findAll(),
                'msg'=>'okk',
            ]);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Service $service,ServiceRepository $serviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'msg'=>'okk',
        ]);
    }
}
