<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProspectController extends AbstractController
{
    /**
     * @Route("/prospect", name="prospect")
     */
    public function index()
    {
        return $this->render('prospect/index.html.twig', [
            'controller_name' => 'ProspectController',
        ]);
    }
}
