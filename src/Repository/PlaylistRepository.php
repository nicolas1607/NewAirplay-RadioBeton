<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Playlist::class);
        $this->em = $em;
    }

    // /**
    //  * @return [] Playlist Retournes la liste des playlists recherchées
    //  */
    // public function findAnimator(): array
    // {
    //     return $this->getEntityManager()
    //         ->createQuery(
    //             "SELECT pl.animator FROM App:playlist pl
    //             GROUP BY pl.animator"
    //         )->getResult();
    // }

    // /**
    //  * @return [] Playlist Retournes la liste des playlists recherchées
    //  */
    public function search($name, $animator, $date, $orderBy, $order): object
    {
        // $search = "SELECT p FROM App:playlist p ";

        // if ($name) {
        //     $search .= "WHERE p.name LIKE '%" . $name . "%'";
        // }
        // if ($animator) {
        //     if (str_contains($search, 'WHERE')) {
        //         $search .= " AND p.animator LIKE '%" . $animator . "%'";
        //     } else {
        //         $search .= "WHERE p.animator LIKE '%" . $animator . "%'";
        //     }
        // }
        // if ($date) {
        //     if (str_contains($search, 'WHERE')) {
        //         $search .= " AND p.entry_date = '" . $date->format('Y-m-d H:i:s') . "'";
        //     } else {
        //         $search .= "WHERE p.entry_date = '" . $date->format('Y-m-d H:i:s') . "'";
        //     }
        // }
        // $search .= " ORDER BY p.entry_date DESC";

        // // return $this->getEntityManager()
        // //     ->createQuery($search)->getResult();
        // return $this->getEntityManager()
        //     ->createQuery($search);

        $search = $this->em->createQueryBuilder();

        $search->select('pl')
            ->from('App\Entity\Playlist', 'pl');

        if ($name) {
            $search->andWhere(
                $search->expr()->like('pl.name', $search->expr()->literal('%' . $name . '%')),
            );
        }

        if ($animator) {
            $search->andWhere(
                $search->expr()->like('pl.animator', $search->expr()->literal('%' . $animator . '%')),
            );
        }

        if ($date) {
            $search->andWhere(
                $search->expr()->eq('pl.entry_date', $search->expr()->literal($date->format('Y-m-d H:i:s'))),
            );
        }

        if ($orderBy) {
            if ($orderBy === 'arrival') {
                $search->orderBy('pl.entry_date', $order);
            } elseif ($orderBy === 'animator') {
                $search->orderBy('pl.animator', $order);
            } elseif ($orderBy === 'name') {
                $search->orderBy('pl.name', $order);
            }
        }

        return $search->getQuery();
    }
}
