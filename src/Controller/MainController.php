<?php

namespace App\Controller;

use App\Form\MainType;
use App\Repository\SortieRepository;
use DateTime;
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

//        $campus = $this->getUser() ->getEstRattacheA() -> getNom();
//        dump($campus);

        $searchOptions['user'] = $this->getUser();
        $searchOptions['campus'] = 1;
        $searchOptions['searchName'] = '';
        $searchOptions['dateDebut'] = null;
        $searchOptions['dateFin'] = null;
        $searchOptions['isOrganisateur'] = false;
        $searchOptions['isInscrit'] = true;
        $searchOptions['isNotInscrit'] = true;
        $searchOptions['isPassed'] = false;
        $searchOptions['etat'] = null;
        $sorties = $sortieRepository -> getSorties($searchOptions);

        dump($searchOptions);


        $mainForm = $this->createForm(MainType::class, $searchOptions);
        $mainForm->handleRequest($request);

        if($mainForm->isSubmitted() && $mainForm->isValid()) {
            dump($searchOptions);
            dump($sorties);
            if ($mainForm->getClickedButton() && 'search' === $mainForm->getClickedButton()->getName()) {

                $dateDebut = $mainForm->get('dateDebut')->getData();
                $dateFin = $mainForm->get('dateFin')->getData();
                if ($dateDebut != null && $dateFin != null){

                    if ($dateDebut > $dateFin){
                        $temp = $dateDebut;
                        $dateDebut = $dateFin;
                        $dateFin = $temp;
                    }
                }

                $searchOptions['campus'] = $mainForm->get('siteOrganisateur')->getData()->getID();
                $searchOptions['searchName'] =  $mainForm->get('searchName')->getData();
                $searchOptions['dateDebut'] = $dateDebut;
                $searchOptions['dateFin'] = $dateFin;
                $searchOptions['isOrganisateur'] = $mainForm->get('isOrganisateur')->getData();
                $searchOptions['isInscrit'] = $mainForm->get('isInscrit')->getData();
                $searchOptions['isNotInscrit'] = $mainForm->get('isNotInscrit')->getData();
                $searchOptions['isPassed'] = $mainForm->get('isPassed')->getData();
                $sorties = $sortieRepository -> getSorties($searchOptions);
                dump($searchOptions);

            }
            if ($mainForm->getClickedButton() && 'create' === $mainForm->getClickedButton()->getName()) {
                return $this->redirect('sortie/create/0');
            }

            dump($searchOptions);


        }

        $mainForm = $this->createForm(MainType::class, $searchOptions);

        return $this->render('main/accueil.html.twig', [
            'mainForm' => $mainForm->createView(),
            'mesSorties' => $sorties,
            'maintenant' => new DateTime(),
        ]);
    }
}
