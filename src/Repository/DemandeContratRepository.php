<?php

namespace App\Repository;

use App\Entity\DemandeContrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DemandeContrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeContrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeContrat[]    findAll()
 * @method DemandeContrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeContrat::class);
    }

    // /**
    //  * @return DemandeContrat[] Returns an array of DemandeContrat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DemandeContrat
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
