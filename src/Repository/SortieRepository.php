<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getSorties(array $options)
    {
        $queryBuilder = $this->createQueryBuilder('s');

//        $queryBuilder->leftJoin('s.inclus', 'i');
//        $queryBuilder->leftJoin('i.estInscrit', 'p');
//        $queryBuilder->leftJoin('s.siteOrganisateur', 'si');
//        $queryBuilder->leftJoin('s.organisateur', 'o');
//        $queryBuilder->leftJoin('s.etat', 'eta');

//        $queryBuilder->andWhere('si.id = :options["campus"]');
//        $queryBuilder->andWhere('s.nom like :options["searchName"]');
//        $queryBuilder->andWhere('s.dateHeureDebut <= :options["dateFin"]');
//        $queryBuilder->andWhere('s.dateHeureDebut >= :options["dateDebut"]');
//        if ($options["isPassed"]) {
//            $queryBuilder->andWhere('e.libelle = "Passée');
//        }
        if ($options["isOrganisateur"]) {
           $queryBuilder->andWhere('s.organisateur = :id' );
           $queryBuilder->setParameter('id',$options["user"])    ;
        }
        $queryBuilder->addOrderBy('s.dateHeureDebut','ASC');
        $query = $queryBuilder->getQuery();
        dump($query->getDQL());
        return $query->getResult();
    }
}
