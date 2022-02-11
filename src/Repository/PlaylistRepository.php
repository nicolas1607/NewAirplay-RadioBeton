<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
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
    public function search($name, $animator, $date): object
    {
        $search = "SELECT p FROM App:playlist p ";

        if ($name) {
            $search .= "WHERE p.name LIKE '%" . $name . "%'";
        }
        if ($animator) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND p.animator LIKE '%" . $animator . "%'";
            } else {
                $search .= "WHERE p.animator LIKE '%" . $animator . "%'";
            }
        }
        if ($date) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND p.entry_date = '" . $date->format('Y-m-d H:i:s') . "'";
            } else {
                $search .= "WHERE p.entry_date = '" . $date->format('Y-m-d H:i:s') . "'";
            }
        }
        $search .= " ORDER BY p.entry_date DESC";

        // return $this->getEntityManager()
        //     ->createQuery($search)->getResult();
        return $this->getEntityManager()
            ->createQuery($search);
    }
}
