<?php

namespace App\Repository;

use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contrat>
 *
 * @method Contrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrat[]    findAll()
 * @method Contrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }
    public function add(Contrat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contrat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getContratAppartement($userId, $etat)
    {
        $sql = $this->createQueryBuilder('c')
            ->innerJoin('c.Appartement', 'a')
            ->innerJoin('a.batis', 'b')
            ->innerJoin('b.proprietaire', 'p')
            ->andWhere('p.code = :val')
            ->setParameter('val', $userId);

        if ($etat != null) {
            $sql->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }
        return $sql->getQuery()
            ->getResult();
    }
    public function getContratLocataire($userId, $etat)
    {
        $sql = $this->createQueryBuilder('c')
            ->innerJoin('c.Appartement', 'a')
            ->innerJoin('c.locataire', 'l')
            ->innerJoin('a.batis', 'b')
            ->innerJoin('b.proprietaire', 'p')
            ->andWhere('l is not null')
            ->andWhere('p.code = :val')
            ->setParameter('val', $userId);

        if ($etat != null) {
            $sql->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }
        return $sql->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Contrat[] Returns an array of Contrat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contrat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
