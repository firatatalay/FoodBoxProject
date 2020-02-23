<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Ordering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ordering|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordering|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordering[]    findAll()
 * @method Ordering[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordering::class);
    }

    // /**
    //  * @return Ordering[] Returns an array of Ordering objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ordering
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAllOrderings(): array
    {
        $conn =$this->getEntityManager()->getConnection();
        $sql= 'SELECT C.*,u.name,u.surname,r.title as rest, f.title as food  FROM ordering c
        join user u on u.id = c.userid
        join food r ON r.id = c.foodid 
        join foood f ON f.id = c.fooodid
        ORDER BY c.id DESC';
        $smt =$conn->prepare($sql);
        $smt->execute();
        return $smt->fetchAll();
    }



    public function getAllOrderingsUser($userid): array
    {
        $conn =$this->getEntityManager()->getConnection();
        $sql= 'SELECT C.*,u.name,u.surname,r.title as rest, f.title as food  FROM ordering c
        join user u on u.id = c.userid
        join food r ON r.id = c.foodid 
        join foood f ON f.id = c.fooodid
        where u.id= :id
        ORDER BY c.id DESC';
        $smt =$conn->prepare($sql);
        $smt->execute(['id'=> $userid]);
        return $smt->fetchAll();
    }


}
