<?php

namespace App\Repository;

use App\Entity\Disc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Disc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disc[]    findAll()
 * @method Disc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disc::class);
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
    //  * @return [] Disc Retournes la liste des disques recherchés
    //  */
    public function search($numInventory, $album, $groupe): array
    {
        $search = "SELECT d FROM App:disc d ";

        if ($numInventory) {
            $search .= "WHERE d.num_inventory = " . $numInventory;
        }
        if ($album) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.album LIKE '%" . $album . "%'";
            } else {
                $search .= "WHERE d.album LIKE '%" . $album . "%'";
            }
        }
        if ($groupe) {
            if (str_contains($search, 'WHERE')) {
                $search .= " AND d.groupe LIKE '%" . $groupe . "%'";
            } else {
                $search .= "WHERE d.groupe LIKE '%" . $groupe . "%'";
            }
        }
        $search .= " ORDER BY d.num_inventory DESC";

        return $this->getEntityManager()
            ->createQuery($search)->getResult();
    }

    // /**
    //  * @return [] Disc Retournes la liste des disques et leur nb de passage en playlist
    //  */
    public function findNbPassagePerDisc($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    {
        $search = "SELECT d FROM App:disc d 
                   LEFT JOIN App:nationality n
                   WITH d.nationality = n.id
                   LEFT JOIN App:language l 
                   WITH d.language = l.id ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb);

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
    public function findStatGenre($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $nb)[0][1];

        var_dump($count);

        $search = "SELECT g.lib, count(d.genre), (count(d.genre)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:genre g
                   WITH g.id = d.genre ";


        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb);
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
    public function findStatNatio($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $nb)[0][1];

        var_dump($count);

        $search = "SELECT n.lib, count(d.nationality), (count(d.nationality)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:nationality n
                   WITH n.id = d.nationality ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb);
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
    public function findStatType($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    {
        if ($nb) $count = $nb;
        else $count = $this->findCount($animator, $startDate, $endDate, $date, $natio, $language, $nb)[0][1];

        var_dump($count);

        $search = "SELECT t.lib, count(d.type), (count(d.type)*100 / " . $count . ")
                   FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist 
                   INNER JOIN App:type t
                   WITH t.id = d.type ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb);
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
    public function findCount($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    {
        $search = "SELECT count(d) FROM App:disc d 
                   INNER JOIN App:playlisthasdisc phd 
                   WITH d.id = phd.disc
                   INNER JOIN App:playlist pl
                   WITH pl.id = phd.playlist ";

        $search = $this->statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb);

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






    // public function test($animator, $startDate, $endDate, $date, $natio, $language, $nb)
    // {
    //     $request = $this->getEntityManager()->createQueryBuilder();
    //     $request->select('d')
    //         // ->addSelect('SIZE(pl.discs) as number')
    //         ->from('App\Entity\Disc', 'd')
    //         ->join('d.nationality', 'n')
    //         ->join('d.language', 'l')
    //         ->join('d.playlist', 'pl');

    //     if ($animator) {
    //         $request->andWhere('d.animator = :animator')
    //             ->setParameter('animator', $animator);
    //     }
    //     if ($startDate) {
    //         $request->andWhere('pl.entry_date > :startDate')
    //             ->setParameter('startDate', $startDate);
    //     }
    //     if ($startDate) {
    //         $request->andWhere('pl.entry_date < :endDate')
    //             ->setParameter('endDate', $endDate);
    //     }
    //     if ($date) {
    //         $request->andWhere('d.leave_date = :leaveDate')
    //             ->setParameter('leaveDate', $date);
    //     }
    //     if ($natio) {
    //         $request->andWhere('n.lib = :natio')
    //             ->setParameter('natio', $natio);
    //     }
    //     if ($language) {
    //         $request->andWhere('l.lib = :language')
    //             ->setParameter('language', $language);
    //     }
    //     if ($nb) {
    //         $request->setMaxResults(':nb')
    //             ->setParameter('nb', $nb);
    //     }

    //     var_dump($request->getQuery()->getResult());

    //     return $request->getQuery()->getResult();
    // }


    // /**
    //  * @return [] Disc Retournes la liste des disques recherchés pour les statistiques
    //  */
    public function statistics($search, $animator, $startDate, $endDate, $date, $natio, $language, $nb)
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
                $search .= " AND pl.entry_date = '" . $date . "'";
            } else {
                $search .= "WHERE pl.entry_date = '" . $date . "'";
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
        $search .= "AND d.num_inventory != '' ";
        return $search;
    }
}
