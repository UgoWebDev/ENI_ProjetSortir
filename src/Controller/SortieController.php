<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,

    ): Response
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $campus = $this->getUser() -> getEstRattacheA();
            $sortie -> setSiteOrganisateur($campus);
            $sortie -> setOrganisateur($this->getUser());
            dump($sortie);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie est bien oublié!');
            return $this->redirectToRoute('main_home');
        }
    dump($sortieForm);
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
        ]);
    }


    #[Route('/details/{id}', name: 'details', requirements: ['page' => '\d+'])]
    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if(!$sortie){
            throw $this->createNotFoundException('La sortie que vous souhaitez afficher n\'hésiste pas!');
        }

        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        return $this->render('sortie/update.html.twig');
    }


    //Patrick

    #[Route('/register/{id}', name: 'register', requirements: ['page' => '\d+'])]
    public function register($id): Response
    {
        return $this->render('sortie/update.html.twig');
    }

    #[Route('/desist/{id}', name: 'desist', requirements: ['page' => '\d+'])]
    public function desist($id): Response
    {
        return $this->render('sortie/desist.html.twig');
    }

    #[Route('/publish/{id}', name: 'publish', requirements: ['page' => '\d+'])]
    public function publish($id): Response
    {
        return $this->render('sortie/publish.html.twig');
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['page' => '\d+'])]
    public function delete($id): Response
    {
        return $this->render('sortie/delete.html.twig');
    }




}
