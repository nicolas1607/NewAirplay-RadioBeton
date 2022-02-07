<?php

namespace App\Repository;

use App\Entity\PlaylistHasDisc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaylistHasDisc|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistHasDisc|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistHasDisc[]    findAll()
 * @method PlaylistHasDisc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistHasDiscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaylistHasDisc::class);
    }

    // /**
    //  * @return PlaylistHasDisc[] Returns an array of PlaylistHasDisc objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlaylistHasDisc
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
