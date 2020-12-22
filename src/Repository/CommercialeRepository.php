<?php

namespace App\Repository;

use App\Entity\Commerciale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commerciale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commerciale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commerciale[]    findAll()
 * @method Commerciale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommercialeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commerciale::class);
    }

    // /**
    //  * @return Commerciale[] Returns an array of Commerciale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commerciale
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
