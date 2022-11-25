<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\Participant;
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

        $queryBuilder->leftJoin('s.inscriptions', 'i');
        $queryBuilder->leftJoin('s.siteOrganisateur', 'si');
        $queryBuilder->leftJoin('s.organisateur', 'o');
        $queryBuilder->leftJoin('s.etat', 'e');
        $queryBuilder->leftJoin('s.lieu', 'l');

        $queryBuilder->addSelect('i');
        $queryBuilder->addSelect('si');
        $queryBuilder->addSelect('o');
        $queryBuilder->addSelect('e');
        $queryBuilder->addSelect('l');

        $queryBuilder->andWhere('s.siteOrganisateur = :camp');
        $queryBuilder->setParameter('camp',$options["campus"]);
        $queryBuilder->andWhere('s.nom like :rec');
        $queryBuilder->setParameter('rec','%'.$options["searchName"].'%');
        if ($options["dateDebut"]) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :deb');
            $queryBuilder->setParameter('deb', $options["dateDebut"]);
        }
        if ($options["dateFin"]) {
            $queryBuilder->andWhere('s.dateHeureDebut <= :fin');
            $queryBuilder->setParameter('fin', $options["dateFin"]);
        }

        if ($options["isPassed"]) {
            $queryBuilder->andWhere('s.etat = :etat');
            $queryBuilder->setParameter('etat',5)    ;
        }
        if ($options["isOrganisateur"]) {
           $queryBuilder->andWhere('s.organisateur = :id' );
           $queryBuilder->setParameter('id',$options["user"])    ;
        }
//        dump($options["isInscrit"]);
//        dump($options["isNotInscrit"]);
//        dump(!$options["isInscrit"] || !$options["isNotInscrit"]);
        if (!$options["isInscrit"] || !$options["isNotInscrit"]) {
            if ($options["isInscrit"]) {
                $queryBuilder->andWhere('i.estInscrit = :ido');
                $queryBuilder->setParameter('ido', $options["user"]);
            }
            if ($options["isNotInscrit"]) {
                $queryBuilder->andWhere('i.estInscrit != :idn');
                $queryBuilder->setParameter('idn', $options["user"]);
            }
        }

        $queryBuilder->addOrderBy('s.dateHeureDebut','ASC');
        $query = $queryBuilder->getQuery();
        dump($query->getDQL());
        return $query->getResult();
    }
}
