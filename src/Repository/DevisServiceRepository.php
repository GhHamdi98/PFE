<?php

namespace App\Repository;

use App\Entity\DevisService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DevisService|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisService|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisService[]    findAll()
 * @method DevisService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisService::class);
    }

    // /**
    //  * @return DevisService[] Returns an array of DevisService objects
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
    public function findOneBySomeField($value): ?DevisService
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
