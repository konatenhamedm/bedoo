<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }
    public function add(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFactureLocataire($locataire)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.contrat', 'c')
            ->innerJoin('c.locataire', 'l')
            ->andWhere('l.code = :val')
            ->setParameter('val', $locataire)
            ->getQuery()
            ->getResult();
    }
    public function getFactureProprietaire($proprietaire)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.contrat', 'c')
            ->innerJoin('c.proprietaire', 'p')
            ->andWhere('p.code = :val')
            ->setParameter('val', $proprietaire)
            ->getQuery()
            ->getResult();
    }

    public function getFacturePro($userId, $etat)
    {
        $sql = $this->createQueryBuilder('f')
            ->innerJoin('f.contrat', 'c')
            ->innerJoin('c.Appartement', 'a')
            ->innerJoin('a.batis', 'b')
            ->innerJoin('b.proprietaire', 'p')
            ->andWhere('p.code = :val')
            ->setParameter('val', $userId);

        if ($etat != null) {
            $sql->andWhere('f.statut = :etat')
                ->setParameter('etat', $etat);
        }
        return $sql->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Facture[] Returns an array of Facture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Facture
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
