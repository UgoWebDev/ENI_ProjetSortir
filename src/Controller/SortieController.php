<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateExitType;
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
    ): Response
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(CreateExitType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie est bien publié!');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }


    #[Route('/details/{id}', name: 'details')]
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
}
