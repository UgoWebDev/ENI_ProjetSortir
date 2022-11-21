<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Entity\Participant;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
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
        //EntityManagerInterface $entityManager,
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

            $sortieRepository->save($sortie, true);

            /*$entityManager->persist($sortie);
            $entityManager->flush();*/

            $this->addFlash('success', 'La sortie est bien enregistré!');
            return $this->redirectToRoute('main_home');
        }
    dump($sortieForm);
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
        ]);
    }

    #[Route('/lieu/{id}', name: 'lieu')]
    public function lieu(Lieu $lieu): Response
    {
        return $this->render('sortie/create.html.twig', [
            'lieu' => $lieu,
        ]);
    }


    #[Route('/details/{id}', name: 'details', requirements: ['page' => '\d+'])]
    //Param converteur de sortie et tout est géré par symfony
    public function details(Sortie $sortie): Response
    {
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
    public function register(
        int $id,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() != 2) {
            $this->addFlash('fail', "L'état initial n'est pas ouvert !");
        } elseif ($sortie->getDateLimiteInscription() < new \DateTime('now')) {
            $this->addFlash('fail', "Impossible de sinscrire à une sortie après la date limite !");
        } elseif ($sortie->getNbInscriptionsMax()  <= $sortie->getInscriptions()->count()) {
            $this->addFlash('fail', "Impossible de sinscrire à une sortie déjà pleine !");
        } else {
            $inscription = new Inscription;
            $inscription->setDateInscription(new \DateTime('now'));
            $inscription->setEstInscrit($this->getUser());
            $inscription->setInclus($sortie);
            $sortie->addInscription($inscription);
            $entityManager->persist($sortie);
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes bien inscrit à la sortie!');
        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/desist/{id}', name: 'desist', requirements: ['page' => '\d+'])]
    public function desist(
        int                    $id,
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        InscriptionRepository  $inscriptionRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if (($sortie->getEtat()->getId() != 2) or ($sortie->getEtat()->getId() != 3) ){
            $this->addFlash('fail', "L'état initial n'est pas ouvert ou cloturé !");
        } else {
            $inscription = $inscriptionRepository->findOneBy([
                'inclus'  => $sortie,
                'estInscrit' => $this->getUser(),
            ]);
            $sortie->removeInscription($inscription);
            $inscriptions = $sortie->getInscriptions();
            $inscriptions->remove($inscription);
            $entityManager->persist($sortie);
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes bien désisté de la sortie!');
        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/publish/{id}', name: 'publish', requirements: ['page' => '\d+'])]
    public function publish(
        int $id,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,

    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() != 1) {
            $this->addFlash('fail', "L'état initial n'est pas en création !");
        } elseif ($sortie->getOrganisateur() !== $this->getUser()) {
            $this->addFlash('fail', "Impossible de publier une sortie que vous n'avez pas créée !");
        } else {
            $etat = $etatRepository->find(2);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie est bien publié!');

        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['page' => '\d+'])]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,

    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() > 3) {
            $this->addFlash('fail', "Impossible de supprimer une sortie une fois qu'elle a commencée !");
        } elseif ($sortie->getOrganisateur() !== $this->getUser()) {
            $this->addFlash('fail', "Impossible de supprimer une sortie que vous n'avez pas créée !");
        } else {
            $etat = $etatRepository->find(6);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie est bien supprimée!');

        }
        return $this->redirectToRoute('main_home');
    }




}
