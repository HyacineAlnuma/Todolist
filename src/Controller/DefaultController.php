<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function indexAction()
    {
        return $this->redirectToRoute('homepage');
    }

    #[Route('/home', name: 'homepage')]
    public function hompage()
    {  
        return $this->render('default/index.html.twig');
    }
}
