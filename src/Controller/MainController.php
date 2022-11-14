<?php

namespace App\Controller;

use App\Form\MainType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/main', name: 'main_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(
        Request $request,
        EntityManagerInterface $entityManager): Response
    {
        $searchOptions = [];
        $searchOptions['campus'] = 1;
        $searchOptions['recherche'] = '';
        $searchOptions['dateDebut'] = null;
        $searchOptions['dateFin'] = null;
        $searchOptions['isOrganisateur'] = true;
        $searchOptions['isInscrit'] = true;
        $searchOptions['isNotInscrit'] = true;
        $searchOptions['isPassed'] = false;

        $mainForm = $this->createForm(MainType::class, $searchOptions);
        $mainForm->handleRequest($request);


        return $this->render('main/accueil.html.twig', [
            'mainForm' => $mainForm->createView()
        ]);
    }
}
