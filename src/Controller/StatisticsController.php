<?php

namespace App\Controller;

use App\Entity\Nationality;
use App\Repository\DiscRepository;
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
            $resultsGenre = $this->discRepo->findStatGenre($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsNatio = $this->discRepo->findStatNatio($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsType = $this->discRepo->findStatType($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            return $this->render('statistics/statistics.html.twig', [
                'nationalities' => $nationalities,
                'resultsGenre' => $resultsGenre,
                'resultsNatio' => $resultsNatio,
                'resultsType' => $resultsType
            ]);
        }

        // Nombre de passage par disque
        elseif ($classement == 'nbPerDisc') {
            $results = $this->discRepo->findNbPassagePerDisc($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            return $this->render('statistics/perdisc.html.twig', [
                'nationalities' => $nationalities,
                'results' => $results
            ]);
        }

        // New classement
        else {
            return $this->render('statistics/index.html.twig', [
                'nationalities' => $nationalities,
                'results' => null
            ]);
        }
    }
}
