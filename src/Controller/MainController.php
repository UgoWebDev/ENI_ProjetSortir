<?php

namespace App\Controller;

use App\Form\MainType;
use App\Repository\SortieRepository;
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
            $searchOptions['sorties'] = $sortieRepository -> getSorties($searchOptions);


        dump($searchOptions);

        $mainForm = $this->createForm(MainType::class, $searchOptions);
        $mainForm->handleRequest($request);

        if($mainForm->isSubmitted() && $mainForm->isValid()) {
            if ($mainForm->getClickedButton() && 'search' === $mainForm->getClickedButton()->getName()) {
                $searchOptions['campus'] = $mainForm->get('siteOrganisateur')->getData()->getID();
                $searchOptions['searchName'] = $mainForm->get('searchName')->getData();
                $searchOptions['dateDebut'] = $mainForm->get('dateDebut')->getData();
                $searchOptions['dateFin'] = $mainForm->get('dateFin')->getData();
                $searchOptions['isOrganisateur'] = $mainForm->get('isOrganisateur')->getData();
                $searchOptions['isInscrit'] = $mainForm->get('isInscrit')->getData();
                $searchOptions['isNotInscrit'] = $mainForm->get('isNotInscrit')->getData();
                $searchOptions['isPassed'] = $mainForm->get('isPassed')->getData();
                $searchOptions['sorties'] = $sortieRepository -> getSorties($searchOptions);
            }
            if ($mainForm->getClickedButton() && 'create' === $mainForm->getClickedButton()->getName()) {
                return $this->redirectToRoute('sortie_create');
            }

            dump($searchOptions);

        }

        return $this->render('main/accueil.html.twig', [
            'mainForm' => $mainForm->createView()
        ]);
    }
}
