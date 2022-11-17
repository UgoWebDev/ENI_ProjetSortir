<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\MainType;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'main_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository
    ): Response
    {

            $searchOptions['campus'] = 1;
            $searchOptions['searchName'] = '';
            $searchOptions['dateDebut'] = null;
            $searchOptions['dateFin'] = null;
            $searchOptions['isOrganisateur'] = true;
            $searchOptions['isInscrit'] = true;
            $searchOptions['isNotInscrit'] = true;
            $searchOptions['isPassed'] = false;
            $sorties = $sortieRepository -> getSorties($searchOptions);

        dump($searchOptions);
        dump($sorties[0]);
        dump($sorties);

        $mainForm = $this->createForm(MainType::class, $searchOptions);
        $mainForm->handleRequest($request);

        if($mainForm->isSubmitted() && $mainForm->isValid()) {
            dump($searchOptions);
            dump($sorties);
            if ($mainForm->getClickedButton() && 'search' === $mainForm->getClickedButton()->getName()) {
                $searchOptions['campus'] = $mainForm->get('siteOrganisateur')->getData()->getID();
                $searchOptions['searchName'] = $mainForm->get('searchName')->getData();
                $searchOptions['dateDebut'] = $mainForm->get('dateDebut')->getData();
                $searchOptions['dateFin'] = $mainForm->get('dateFin')->getData();
                $searchOptions['isOrganisateur'] = $mainForm->get('isOrganisateur')->getData();
                $searchOptions['isInscrit'] = $mainForm->get('isInscrit')->getData();
                $searchOptions['isNotInscrit'] = $mainForm->get('isNotInscrit')->getData();
                $searchOptions['isPassed'] = $mainForm->get('isPassed')->getData();
                $sorties = $sortieRepository -> getSorties($searchOptions);
                dump($searchOptions);
                dump($sorties[0]);
                dump($sorties);

            }
            if ($mainForm->getClickedButton() && 'create' === $mainForm->getClickedButton()->getName()) {
                return $this->redirectToRoute('sortie_create');
            }

            dump($searchOptions);
            dump($sorties);

        }

        return $this->render('main/accueil.html.twig', [
            'mainForm' => $mainForm->createView(),
            'mesSorties' => $sorties,
        ]);
    }

    #[Route('/ville', name: 'ville')]
    public function ville(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties = $sortieRepository->findAll();
//        dump($sorties);
        $defauts = [];
        $count = 0;
        $count2 = 0;

        foreach ($sorties as $sortie) {
            $count++;
            $nbInscrits =  sizeof( $sortie->getInscriptions());
            $nbPlaces = $sortie->getNbInscriptionsMax() ;
            if ($nbInscrits > $nbPlaces) {
                $count2++;
                $defauts[]=$sortie;
            }
        }
        dump($defauts);
        dump($count);
        dump($count2);

        return $this->render('main/ville.html.twig', [
            'defauts' => $defauts,
        ]);
    }
}
