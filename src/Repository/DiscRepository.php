<?php

namespace App\Repository;

use App\Entity\Disc;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Disc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disc[]    findAll()
 * @method Disc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Disc::class);
        $this->em = $em;
    }

    public function findAllAlbum(): array
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT d.album FROM App:disc d"
            )
            ->getResult();
    }

    // /**
    //  * @return int Retournes un nouveau numéro d'inventaire
    //  */
    public function generateNumInventory(): int
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT max(d.num_inventory) FROM App:disc d"
            )
            ->getResult()[0][1] + 1;
    }

    // /**
    //  * @return int Retournes la liste des emprunteurs
    //  */
    public function getLeaveNames(): array
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT d.leave_name FROM App:disc d WHERE d.leave_name!='NULL' GROUP BY d.leave_name"
            )
            ->getResult();
    }

    // /**
    //  * @return [] Disc Retournes la liste des disques recherchés
    //  */
    public function search($numInventory, $album, $groupe, $orderBy, $order): object
    {
        // $search = "SELECT d FROM App:disc d ";
        
        // if ($numInventory) {
        //     $search .= "WHERE d.num_inventory = '" . $numInventory . "'";
        // }
        // elseif ($numInventory === 0) 
        // {
        //     $search .= "WHERE d.num_inventory = '" . $numInventory . "'";
        // }

        // if ($album) {
        //     if (str_contains($search, 'WHERE')) {
        //         $search .= " AND d.album LIKE '%" . $album . "%'";
        //     } else {
        //         $search .= "WHERE d.album LIKE '%" . $album . "%'";
        //     }
        // }

        // if ($groupe) {
        //     if (str_contains($search, 'WHERE')) {
        //         $search .= " AND d.groupe LIKE '%" . $groupe . "%'";
        //     } else {
        //         $search .= "WHERE d.groupe LIKE '%" . $groupe . "%'";
        //     }
        // }
        // $search .= " ORDER BY d.num_inventory DESC";

        // return $this->getEntityManager()
        //     ->createQuery($search);

        $search = $this->em->createQueryBuilder();

        $search->select('d')
               ->from('App\Entity\Disc', 'd');
        
        if($numInventory)
        {
            $search->andWhere(
                $search->expr()->eq('d.num_inventory', $search->expr()->literal($numInventory)),
            );
        }
        elseif($numInventory === 0)
        {
            $search->andWhere(
                $search->expr()->eq('d.num_inventory', $search->expr()->literal($numInventory)),
            );
        }

        if($album)
        {
            $search->andWhere(
                $search->expr()->like('d.album', $search->expr()->literal('%'.$album.'%')),
            );
        }

        if($groupe)
        {
            $search->andWhere(
                $search->expr()->like('d.groupe', $search->expr()->literal('%'.$groupe.'%')),
            );
        }

        if($orderBy)
        {
            if($orderBy === 'arrival')
            {
                $search->orderBy('d.arrival_date', $order);
            }
            elseif($orderBy === 'leave')
            {
                $search->orderBy('d.leave_date', $order);
            }
            elseif($orderBy === 'group')
            {
                $search->orderBy('d.groupe', $order);
            }
            elseif($orderBy === 'label')
            {
                $search->orderBy('d.label', $order);
            }
            elseif($orderBy === 'album')
            {
                $search->orderBy('d.album', $order);
            }
        }

        return $search->getQuery();
    }

    // /**
    //  * @return [] Disc Retournes la liste des disques et leur nb de passage en playlist
    //  */
    public function findNbPassagePerDisc($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        $search = "SELECT count(d.id) as nb, n.id as natio, d.groupe, d.album, d.label 
        FROM App:disc d 
        INNER JOIN App:playlisthasdisc phd 
        WITH d.id = phd.disc
        INNER JOIN App:playlist pl
        WITH pl.id = phd.playlist 
        INNER JOIN App:nationality n
        WITH n.id = d.nationality ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb);
        $search .= " GROUP BY d.id ORDER BY count(d.id) DESC";

        if ($nb) {
            return $this->getEntityManager()
                ->createQuery($search)
                ->setMaxResults($nb)
                ->getResult();
        } else {
            return $this->getEntityManager()
                ->createQuery($search)
                ->getResult();
        }
    }

    // /**
    //  * @return [] Disc Retournes la liste des genres et leur pourcentage
    //  */
    public function findStatGenre($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)[0][1];

        $search = "SELECT g.lib, count(d.genre), (count(d.genre)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:genre g
                   WITH g.id = d.genre ";


        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb);
        $search .= " GROUP BY g.lib";

        if ($nb) {
            return $this->getEntityManager()
                ->createQuery($search)
                ->setMaxResults($nb)
                ->getResult();
        } else {
            return $this->getEntityManager()
                ->createQuery($search)
                ->getResult();
        }
    }

    // /**
    //  * @return [] Disc Retournes la liste des nationalités et leur pourcentage
    //  */
    public function findStatNatio($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)[0][1];

        $search = "SELECT n.lib, count(d.nationality), (count(d.nationality)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:nationality n
                   WITH n.id = d.nationality ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb);
        $search .= " GROUP BY n.lib";

        if ($nb) {
            return $this->getEntityManager()
                ->createQuery($search)
                ->setMaxResults($nb)
                ->getResult();
        } else {
            return $this->getEntityManager()
                ->createQuery($search)
                ->getResult();
        }
    }

    // /**
    //  * @return [] Disc Retournes la liste des types et leur pourcentage
    //  */
    public function findStatType($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)[0][1];

        $search = "SELECT t.lib, count(d.type), (count(d.type)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:type t
                   WITH t.id = d.type ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb);
        $search .= " GROUP BY t.lib";

        if ($nb) {
            return $this->getEntityManager()
                ->createQuery($search)
                ->setMaxResults($nb)
                ->getResult();
        } else {
            return $this->getEntityManager()
                ->createQuery($search)
                ->getResult();
        }
    }

    // /**
    //  * @return [] Disc Retournes la liste des disques recherchés pour les statistiques
    //  */
    public function findCount($animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        $search = "SELECT count(d) FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb);

        if ($nb) {
            return $this->getEntityManager()
                ->createQuery($search)
                ->setMaxResults($nb)
                ->getResult();
        } else {
            return $this->getEntityManager()
                ->createQuery($search)
                ->getResult();
        }
    }

    // /**
    //  * @return [] Disc Ajoutes les paramètres (WHERE/AND <param>) à la requête SQL
    //  */
    public function statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $emprunt, $nb)
    {
        if ($animator) {
            $search .= "WHERE d.animator = " . $animator;
        }
        if ($startDate) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND pl.entry_date > '" . $startDate . "'";
            } else {
                $search .= "WHERE pl.entry_date > '" . $startDate . "'";
            }
            if ($endDate) {
                if (str_contains($search, 'WHERE')) {
                    $search .= " AND pl.entry_date < '" . $endDate . "'";
                } else {
                    $search .= "WHERE pl.entry_date < '" . $endDate . "'";
                }
            }
        } elseif ($endDate) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND pl.entry_date < '" . $endDate . "'";
            } else {
                $search .= "WHERE pl.entry_date < '" . $endDate . "'";
            }
        }
        if ($date) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.leave_date = '" . $date . "'";
            } else {
                $search .= "WHERE d.leave_date = '" . $date . "'";
            }
        }
        if ($natio) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.nationality = '" . $natio . "'";
            } else {
                $search .= "WHERE d.nationality = '" . $natio . "'";
            }
        }
        if ($language) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.language = " . $language;
            } else {
                $search .= "WHERE d.language = " . $language;
            }
        }
        if ($emprunt) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.leave_name = '" . $emprunt . "'";
            } else {
                $search .= "WHERE d.leave_name = '" . $emprunt . "'";
            }
        }
        $search .= " AND d.num_inventory != '' ";
        return $search;
    }

    // public function searchDisc($data)
    // {
    //     $search = $this->em->createQueryBuilder();

    //     $search->select('d')
    //            ->from('App\Entity\Disc', 'd');

    //     if($data)
    //     {
    //         $search->andWhere(
    //             $search->expr()->orX( 
    //                 $search->expr()->eq('d.num_inventory', $search->expr()->literal($data)),
    //                 $search->expr()->eq('d.album', $search->expr()->literal($data))
    //             )
    //         );
    //     }
        
    //     return $search->getQuery()->getResult();
    // }
}
