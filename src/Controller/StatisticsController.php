<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Entity\User;
use App\Entity\Nationality;
use App\Repository\DiscRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticsController extends AbstractController
{
    private EntityManagerInterface $em;
    private DiscRepository $discRepo;

    public function __construct(EntityManagerInterface $em, DiscRepository $discRepo)
    {
        $this->em = $em;
        $this->discRepo = $discRepo;
    }

    /**
     * @Route("/statistics", name="statistics")
     */
    public function index(Request $request): Response
    {
        // $users = $this->em->getRepository(User::class)->findAll();
        $nationalities = $this->em->getRepository(Nationality::class)->findAll();

        $animator = $request->query->get('animator');
        $dateStart = $request->query->get('date_start');
        $dateEnd = $request->query->get('date_end');
        $datePlaylist = $request->query->get('date_playlist');
        $nationality = $request->query->get('nationality');
        $language = $request->query->get('language');
        $name = $request->query->get('name');
        $nb = $request->query->get('number');
        $classement = $request->query->get('classement');

        // Statistiques
        if ($classement == 'stats') {
            // if ($animator || $datePlaylist || $dateStart || $dateEnd || $datePlaylist || $nationality || $language) {
            $resultsGenre = $this->discRepo->findStatGenre($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsNatio = $this->discRepo->findStatNatio($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsType = $this->discRepo->findStatType($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            // }
            return $this->render('statistics/statistics.html.twig', [
                // 'users' => $users,
                'nationalities' => $nationalities,
                'resultsGenre' => $resultsGenre,
                'resultsNatio' => $resultsNatio,
                'resultsType' => $resultsType
            ]);
        }

        // Nombre de passage par disque
        elseif ($classement == 'nbPerDisc') {
            if ($animator || $datePlaylist || $dateStart || $dateEnd || $datePlaylist || $nationality || $language) {
                $results = $this->discRepo->findNbPassagePerDisc($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
                // $results = $this->discRepo->findAll();
                var_dump($results);
            }
            return $this->render('statistics/perdisc.html.twig', [
                // 'users' => $users,
                'nationalities' => $nationalities,
                'results' => $results
            ]);
        }

        // New classement
        else {
            return $this->render('statistics/index.html.twig', [
                // 'users' => $users,
                'nationalities' => $nationalities,
                'results' => null
            ]);
        }
    }
}
