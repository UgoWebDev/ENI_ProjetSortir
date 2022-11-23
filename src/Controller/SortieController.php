<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\DeleteType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create/{id}', name: 'create', requirements: ['page' => '\d+'])]
    public function create(
        Request $request,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        int $id
    ): Response
    {
        if($id == 0){
            $sortie = new Sortie();
            $ville = null;
        }else{
            $sortie = $sortieRepository -> find($id);
            $ville = $sortie-> getLieu() ->getVille()->getNom();
            dump($sortie);
            dump($ville);
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            if($id == 0) {
                $campus = $this->getUser()->getEstRattacheA();
                $sortie->setSiteOrganisateur($campus);
                $sortie->setOrganisateur($this->getUser());

                $etat = $etatRepository->find(1);
                $sortie->setEtat($etat);
            }

            $sortieRepository->save($sortie, true);

            /*$entityManager->persist($sortie);
            $entityManager->flush();*/

            $this->addFlash('success', 'La sortie est bien enregistrée!');
            return $this->redirectToRoute('main_home');
        }
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
            'ville' => $ville,
        ]);
    }

    #[Route('/ville/{id}', name: 'ville')]
    public function ville(
        VilleRepository $villeRepository,
        int $id,
    ): Response
    {
        $ville = $villeRepository->find($id);
        $codePostal = $ville->getCodePostal();

        return new JsonResponse(['codePostal' => $codePostal]);
    }

    #[Route('/lieu/{id}', name: 'lieu')]
    public function lieu(
        LieuRepository $lieuRepository,
        int $id,
    ): Response
    {
        $lieu = $lieuRepository->find($id);
        $rue = $lieu->getRue();
        $latitude = $lieu->getLatitude();
        $longitude = $lieu->getLongitude();

        return new JsonResponse(['rue' => $rue, 'latitude' => $latitude, 'longitude' => $longitude]);
    }


    #[Route('/details/{id}', name: 'details', requirements: ['page' => '\d+'])]
    //Param converteur de sortie et tout est géré par symfony
    public function details(Sortie $sortie): Response
    {
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/update/{id}', name: 'update', requirements: ['page' => '\d+'])]
    public function update(
        SortieRepository $sortieRepository,
        int $id,
    ): Response
    {
        $sortie = $sortieRepository -> find($id);

        return $this->render('sortie/create.html.twig',[
            'sortie' => $sortie,
        ]);
    }



    //Patrick à partir de cette ligne

    #[Route('/register/{id}', name: 'register', requirements: ['page' => '\d+'])]
    public function register(
        int $id,
        SortieRepository $sortieRepository,
        InscriptionRepository  $inscriptionRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($id);

        // Vérifie sir la sortie est ouverte, que la date d'inscription n'est pas passée et qu'il reste de la place
        if ($sortie->getEtat()->getId() != 2) {
            $this->addFlash('fail', "L'état initial n'est pas ouvert !");
        } elseif ($sortie->getDateLimiteInscription() < new DateTime('now')) {
            $this->addFlash('fail', "Impossible de sinscrire à une sortie après la date limite !");
        } elseif ($sortie->getNbInscriptionsMax()  <= $sortie->getInscriptions()->count()) {
            $this->addFlash('fail', "Impossible de s'inscrire à une sortie déjà pleine !");
        } else {
            // Crée l'instance de l'inscription
            $inscription = new Inscription;
            $inscription->setDateInscription(new DateTime('now'));

            // Ajoute l'inscription à la sortie
            $sortie->addInscription($inscription);

            // Ajoute l'inscription au participant
            $this->getUser()->addInscription($inscription);


            // Sauvegarde les entités
            $inscriptionRepository->save($inscription, true);

            $this->addFlash('success', 'Vous êtes bien inscrit à la sortie!');
        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/desist/{id}', name: 'desist', requirements: ['page' => '\d+'])]
    public function desist(
        int                    $id,
        SortieRepository       $sortieRepository,
        InscriptionRepository  $inscriptionRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        dump($sortie);

        // on vérifie que la sortie est bien ouverte ou fermée (en cours)
        if (($sortie->getEtat()->getId() != 2) and ($sortie->getEtat()->getId() != 3) ){
            $this->addFlash('fail', "L'état initial n'est pas ouvert ou cloturé !");
        } else {
            // Récupère l'inscription
            $inscription = $inscriptionRepository->findOneBy([
                'inclus'  => $sortie,
                'estInscrit' => $this->getUser(),
            ]);

            // Supprime l'inscription en cours
            $sortie->removeInscription($inscription);
            $this->getUser()->removeInscription($inscription);

            // sauvegarde l'entité
            $inscriptionRepository->save($inscription, true);

            $this->addFlash('success', 'Vous êtes bien désisté de la sortie!');
        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/publish/{id}', name: 'publish', requirements: ['page' => '\d+'])]
    public function publish(
        int $id,
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
            $sortieRepository->save($sortie, true);
            $this->addFlash('success', 'La sortie est bien publié!');

        }
        return $this->redirectToRoute('main_home');
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['page' => '\d+'])]
    public function delete(
        int $id,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        Request $request,

    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() > 3) {
            $this->addFlash('fail', "Impossible de supprimer une sortie une fois qu'elle a commencée !");
            return $this->redirectToRoute('main_home');
        } elseif ($sortie->getOrganisateur() !== $this->getUser()) {
            $this->addFlash('fail', "Impossible de supprimer une sortie que vous n'avez pas créée !");
            return $this->redirectToRoute('main_home');
        } else {
            $deleteForm = $this->createForm(DeleteType::class, $sortie);
            $deleteForm->handleRequest($request);

            if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
                $sortie->setTxtAnnulation($deleteForm->get('txtAnnulation')->getData());
                $etat = $etatRepository->find(6);
                $sortie->setEtat($etat);
                $sortieRepository->save($sortie, true);
                $this->addFlash('success', 'La sortie est bien supprimée!');
                return $this->redirectToRoute('main_home');
            }

        }
        return $this->render('sortie/delete.html.twig', [
            'commentForm' => $deleteForm->createView(),
            'sortie' => $sortie,
        ]);

    }
}
