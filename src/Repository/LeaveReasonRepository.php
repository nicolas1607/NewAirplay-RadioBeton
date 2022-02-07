<?php

namespace App\Repository;

use App\Entity\LeaveReason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LeaveReason|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeaveReason|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeaveReason[]    findAll()
 * @method LeaveReason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaveReasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveReason::class);
    }

    // /**
    //  * @return LeaveReason[] Returns an array of LeaveReason objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LeaveReason
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
