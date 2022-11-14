<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
