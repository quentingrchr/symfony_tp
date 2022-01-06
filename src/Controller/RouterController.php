<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouterController extends AbstractController
{

    /**
     * @Route("/", name="app_homePage")
     * @return Response
     */
    public function homePage() :Response
    {
        return $this->render('Pages/home.html.twig');
    }
}