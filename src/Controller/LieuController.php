<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{
    #[Route('', name: 'ajout')]
    public function ajout(
        Request $request,
        LieuRepository $lieuRepository,
    ): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this -> createForm(LieuType::class, $lieu);

        $lieuForm -> handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){
            $lieuRepository->save($lieu, true);

            $this->addFlash('success', 'Le lieu est bien enregistré!');
            return $this->redirectToRoute('sortie_create', array('id' =>0) );
        }

        return $this->render('lieu/create.html.twig', [
            'lieu' => $lieu,
            'lieuForm' => $lieuForm->createView(),
        ]);
    }
}
