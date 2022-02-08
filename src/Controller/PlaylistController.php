<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Form\SearchPlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaylistController extends AbstractController
{
    private EntityManagerInterface $em;
    private PlaylistRepository $playlistRepo;

    public function __construct(EntityManagerInterface $em, PlaylistRepository $playlistRepo)
    {
        $this->em = $em;
        $this->playlistRepo = $playlistRepo;
    }

    /**
     * @Route("/playlist", name="playlist")
     */
    public function index(): Response
    {
        return $this->render('playlist/index.html.twig', [
            'controller_name' => 'PlaylistController',
        ]);
    }


    /**
     * @Route("/playlist/add", name="playlist_add")
     */
    public function addPlaylist(Request $request): Response
    {
        // Traitement de la requête envoyée depuis le formulaire de saisie de playlist en fonction de la requête et de l aprésence d'un nom (champ requis)
        if ($request && $request->query->get('name')) {
            $playlist = new Playlist;

            $entryDate = $request->query->get('date');
            $animator = $request->query->get('name');
            $name = $request->query->get('title');

            $playlist->setAnimator($animator)
                ->setName($name)
                ->setEntryDate(new \DateTimeImmutable($entryDate));

            $discs = $request->query->get('discs');

            // 'discs' étant un tableau, injection dans 'playlist_disc' de chaque id 'disc' associé à chaque 'id' de playlist
            $wrongDiscs = [];
            foreach ($discs as $disc) {
                $discObject = $this->em->getRepository(Disc::class)->findOneBy(['num_inventory' => $disc]);
                if ($discObject) {
                    $playlist->addDisc($discObject);
                } else {
                    // $this->addFlash('danger', 'Attention ! le n° d\'inventaire ' . $disc . ' n\'existe pas...');
                    array_push($wrongDiscs, $disc);
                }
            }

            if (count($wrongDiscs) > 0) {
                foreach ($wrongDiscs as $wrongDisc) {
                    $this->addFlash('danger', 'Attention ! le n° d\'inventaire ' . $wrongDisc . ' n\'existe pas...');
                }

                return $this->redirectToRoute('playlist_add');
            } else {
                $this->em->persist($playlist);
                $this->em->flush();

                return $this->redirectToRoute('show_playlist', ['id' => $playlist->getId()]);
            }
        };

        // Récupération des animateurs depuis la table 'playlist', pour ensuite les dédoublonner et les renvoyer vers le front
        $playlists = $this->em->getRepository(Playlist::class)->findAll();
        $animatorsAll = [];
        foreach ($playlists as $playlist) {
            array_push($animatorsAll, $playlist->getAnimator());
        }
        $animators = array_unique($animatorsAll, SORT_REGULAR);

        // $discs = $this->em->getRepository(Disc::class)->findAll();
        // $discsNum = [];
        // foreach ($discs as $disc)
        // {
        //     array_push($discsNum, [$disc->getNumInventory(), $disc->getGroupe(), $disc->getAlbum(), $disc->getId()]);
        // }

        return $this->render('playlist/add.html.twig', [
            'animators' => $animators,
            // 'discs_nums' => $discsNum,
        ]);
    }

    /**
     * @Route("/playlist/search", name="search_playlist")
     */
    public function search(Request $request): Response
    {

        $searchPlaylistForm = $this->createForm(SearchPlaylistType::class);
        $searchPlaylistForm->handleRequest($request);

        if ($searchPlaylistForm->isSubmitted() && $searchPlaylistForm->isValid()) {
            $search = $searchPlaylistForm->getData();
            $playlists = $this->playlistRepo->search($search->getName(), $search->getAnimator(), $search->getEntryDate());

            return $this->render('playlist/search.html.twig', [
                'searchPlaylistForm' => $searchPlaylistForm->createView(),
                'playlists' => $playlists
            ]);
        }

        return $this->render('playlist/search.html.twig', [
            'searchPlaylistForm' => $searchPlaylistForm->createView(),
            'playlists' => null
        ]);
    }

    /**
     * @Route("/playlist/show/{id}", name="show_playlist")
     */
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    /**
     * @Route("/playlist/add/disc/{playlist}", name="add_disc_playlist")
     */
    public function addDisc(Request $request, Playlist $playlist): Response
    {
        if ($request) {

            $discs = $request->query->get('discs');

            foreach ($discs as $disc) {
                $disc = $this->em->getRepository(Disc::class)->findOneBy(['num_inventory' => $disc]);
                if ($disc) {
                    $playlist->addDisc($disc);
                    $disc->addPlaylist($playlist);

                    $this->em->persist($disc);
                    $this->em->persist($playlist);
                } else {
                    // $this->addFlash('')
                }
            }
        }

        $this->em->flush();

        return $this->redirectToRoute('show_playlist', ['id' => $playlist->getId()]);
    }

    /**
     * @Route("/playlist/delete/disc/{playlist}/{disc}", name="delete_disc_playlist")
     */
    public function deleteDisc(Playlist $playlist, Disc $disc): Response
    {
        $playlist->removeDisc($disc);
        $disc->removePlaylist($playlist);

        $this->em->persist($disc);
        $this->em->persist($playlist);

        $this->em->flush();

        return $this->redirectToRoute('show_playlist', ['id' => $playlist->getId()]);
    }

    /**
     * @Route("/playlist/test/{numero}", name="test")
     */
    public function test($numero)
    {
        $disc = $this->em->getRepository(Disc::class)->findOneBy([
            'num_inventory' => $numero
        ]);

        $response = new JsonResponse([
            'id' => $disc->getId(),
            'group' => $disc->getGroupe(),
            'album' => $disc->getAlbum(),
            'inventory_num' => $disc->getNumInventory()
        ]);

        if($response)
        {
            return $response;
        }
    }
}
