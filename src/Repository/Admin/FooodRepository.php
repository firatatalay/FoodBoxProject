<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Foood;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Foood|null find($id, $lockMode = null, $lockVersion = null)
 * @method Foood|null findOneBy(array $criteria, array $orderBy = null)
 * @method Foood[]    findAll()
 * @method Foood[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FooodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Foood::class);
    }

    // /**
    //  * @return Foood[] Returns an array of Foood objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Foood
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
