<?php

namespace App\Repository;

use App\Entity\DevisOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DevisOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisOptions[]    findAll()
 * @method DevisOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisOptions::class);
    }

    // /**
    //  * @return DevisOptions[] Returns an array of DevisOptions objects
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
    public function findOneBySomeField($value): ?DevisOptions
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
