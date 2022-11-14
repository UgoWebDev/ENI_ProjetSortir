<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateExitType;
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

            $this->addFlash('success', 'La sortie est bien ajouté!');
            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }


    #[Route('/', name: 'details')]
    public function details($id): Response
    {
        return $this->render('sortie/details.html.twig');
    }


    #[Route('/', name: 'modif')]
    public function modif(): Response
    {
        return $this->render('sortie/modif.html.twig');
    }
}
