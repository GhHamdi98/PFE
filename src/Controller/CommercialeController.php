<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommercialeController extends AbstractController
{
    /**
     * @Route("/commerciale", name="commerciale")
     */
    public function index()
    {
        return $this->render('commerciale/index.html.twig', [
            'controller_name' => 'CommercialeController',
        ]);
    }
    /**
     * @Route("/aa", name="aa")
     */
    public function aa()
    {
        return $this->render('commerciale/redirect.html.twig');
    }
}
