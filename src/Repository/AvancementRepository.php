<?php

namespace App\Repository;

use App\Entity\Avancement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Avancement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avancement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avancement[]    findAll()
 * @method Avancement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvancementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avancement::class);
    }

    // /**
    //  * @return Avancement[] Returns an array of Avancement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Avancement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
