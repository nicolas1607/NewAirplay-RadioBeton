<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Nationality;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
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
        $format = $request->query->get('format');
        $classement = $request->query->get('classement');

        // Statistiques
        if ($classement == 'stats') {
            $nb = null;
            $resultsGenre = $this->discRepo->findStatGenre($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsNatio = $this->discRepo->findStatNatio($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);
            $resultsType = $this->discRepo->findStatType($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);

            // FORMAT PDF
            if ($format == "pdf") {
                // on defini des options pour le pdf
                $optionsPdf = new Options();
                $optionsPdf->setDefaultFont('Arial');
                $optionsPdf->setIsRemoteEnabled(true);
                $optionsPdf->setIsHtml5ParserEnabled(true);
                $optionsPdf->isRemoteEnabled(true);

                // on instancie domPDf - on lui passe les options
                $domPdf = new Dompdf($optionsPdf);
                $content = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);
                $domPdf->setHttpContext($content);

                // on genere le html avec les données de la fiche
                $html = $this->renderView('statistics/download-pdf.html.twig', [
                    'nationalities' => $nationalities,
                    'resultsGenre' => $resultsGenre,
                    'resultsNatio' => $resultsNatio,
                    'resultsType' => $resultsType,
                    'dateStart' => $dateStart,
                    'dateEnd' => $dateEnd,
                    'datePlaylist' => $datePlaylist,
                    'name' => $name
                ]);

                // on transmet le contenue html dans le domPDF
                $domPdf->loadHtml($html);
                $domPdf->setPaper('A4', 'portrait');
                $domPdf->render();

                // on donne un nom de fichier
                if ($name != "" || $name != null) {
                    $nomPDF = $name . '-' . substr(uniqid(), 0, 4) . '.pdf';
                } elseif ($classement == "stats") {
                    $nomPDF = 'Stats' . '-' . substr(uniqid(), 0, 4) . '.pdf';
                }

                // on le save dans le dossier statistics/pdf
                $pdf = $domPdf->output();
                $file_location = $this->getParameter('pdf') . '/' . $nomPDF;
                file_put_contents($file_location, $pdf);
                $domPdf->stream($nomPDF, [
                    'Attachement' => true,
                ]);
                return new Response();
            }
            // FORMAT EXCEL
            elseif ($format == "excel") {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getCell('A1')->setValue('Catégorie');
                $sheet->getCell('B1')->setValue('Nb disques');
                $sheet->getCell('C1')->setValue('%');

                $sheet->getCell('A2')->setValue('Genre');
                for ($i = 0; $i < count($resultsGenre); $i++) {
                    $disc = $resultsGenre[$i];
                    $sheet->getCell('A' . strval($i + 3))->setValue($disc['lib']);
                    $sheet->getCell('B' . strval($i + 3))->setValue($disc[1]);
                    $sheet->getCell('C' . strval($i + 3))->setValue($disc[2]);
                }
                $sheet->getCell('A7')->setValue('Nationalité');
                for ($i = 0; $i < count($resultsNatio); $i++) {
                    $disc = $resultsNatio[$i];
                    $sheet->getCell('A' . strval($i + 8))->setValue($disc['lib']);
                    $sheet->getCell('B' . strval($i + 8))->setValue($disc[1]);
                    $sheet->getCell('C' . strval($i + 8))->setValue($disc[2]);
                }
                $sheet->getCell('A11')->setValue('Type');
                for ($i = 0; $i < count($resultsType); $i++) {
                    $disc = $resultsType[$i];
                    $sheet->getCell('A' . strval($i + 12))->setValue($disc['lib']);
                    $sheet->getCell('B' . strval($i + 12))->setValue($disc[1]);
                    $sheet->getCell('C' . strval($i + 12))->setValue($disc[2]);
                }

                // Update XLSX
                $writer = new Xlsx($spreadsheet);
                if ($name != "" || $name != null) {
                    $path = $this->getParameter('excel') . '/' . $name . '-' . substr(uniqid(), 0, 4) . '.xlsx';
                } else {
                    $path = $this->getParameter('excel') . '/Stats-' . substr(uniqid(), 0, 4) . '.xlsx';
                }
                $writer->save($path);
                return $this->file($path);
            }
        }

        // Nombre de passage par disque
        elseif ($classement == 'nbPerDisc') {
            $results = $this->discRepo->findNbPassagePerDisc($animator, $dateStart, $dateEnd, $datePlaylist, $nationality, $language, $nb);

            // FORMAT EXCEL
            if ($format == "excel") {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Nombre de passage par disque');
                $sheet->getCell('B1')->setValue('Album');
                $sheet->getCell('C1')->setValue('Groupe');
                $sheet->getCell('D1')->setValue('Label');

                for ($i = 0; $i < count($results); $i++) {
                    $disc = $results[$i];
                    $sheet->getCell('A' . strval($i + 2))->setValue($disc['nb']);
                    $sheet->getCell('B' . strval($i + 2))->setValue($disc['album']);
                    $sheet->getCell('C' . strval($i + 2))->setValue($disc['groupe']);
                    $sheet->getCell('D' . strval($i + 2))->setValue($disc['label']);
                }

                $writer = new Xlsx($spreadsheet);
                if ($name != "" || $name != null) {
                    $path = $this->getParameter('excel') . '/' . $name . '-' . substr(uniqid(), 0, 4) . '.xlsx';
                } else {
                    $path = $this->getParameter('excel') . '/NbPerDisc-' . substr(uniqid(), 0, 4) . '.xlsx';
                }
                $writer->save($path);
                return $this->file($path);
            }
            // FORMAT PDF
            elseif ($format == "pdf") {
                // on defini des options pour le pdf
                $optionsPdf = new Options();
                $optionsPdf->setDefaultFont('Arial');
                $optionsPdf->setIsRemoteEnabled(true);
                $optionsPdf->setIsHtml5ParserEnabled(true);
                $optionsPdf->isRemoteEnabled(true);

                // on instancie domPDf - on lui passe les options
                $domPdf = new Dompdf($optionsPdf);
                $content = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);
                $domPdf->setHttpContext($content);

                // on genere le html avec les données de la fiche
                $html = $this->renderView('statistics/download-pdf-perdisc.html.twig', [
                    'nationalities' => $nationalities,
                    'results' => $results,
                    'dateStart' => $dateStart,
                    'dateEnd' => $dateEnd,
                    'datePlaylist' => $datePlaylist,
                    'name' => $name
                ]);

                // on transmet le contenue html dans le domPDF
                $domPdf->loadHtml($html);
                $domPdf->setPaper('A4', 'portrait');
                $domPdf->render();

                // on donne un nom de fichier
                if ($name != "" || $name != null) {
                    $nomPDF = $name . '-' . substr(uniqid(), 0, 4) . '.pdf';
                } elseif ($classement == "nbPerDisc") {
                    $nomPDF = 'NbPerDisc' . '-' . substr(uniqid(), 0, 4) . '.pdf';
                }

                // on le save dans le dossier statistics/pdf
                $pdf = $domPdf->output();
                $file_location = $this->getParameter('pdf') . '/' . $nomPDF;
                file_put_contents($file_location, $pdf);
                $domPdf->stream($nomPDF, [
                    'Attachement' => true,
                ]);
                return new Response();
            }
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
