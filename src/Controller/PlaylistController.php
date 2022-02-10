<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Entity\PlaylistHasDisc;
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
        $date = $request->query->get('date');
        $name = $request->query->get('title');
        $animator = $request->query->get('name');
        $discs = $request->query->get('discs');

        if($date || $name || $animator || $discs)
        {
            $playlist = new PlayList();

            $playlist->setEntryDate(new \DateTime($date))
                     ->setName($name)
                     ->setAnimator($animator);

            foreach($discs as $id)
            {
                $relation = new PlaylistHasDisc;
                
                $disc = $this->em->getRepository(Disc::class)->findOneBy([
                    'id' => $id
                ]);

                $playlist->addPlaylistHasDisc( $relation->setDisc($disc) );
            }
        
            $this->em->persist($playlist);
            $this->em->flush();

            $this->addFlash(
                'playlist_success',
                'Rock\'n Roll ! Une nouvelle playlist vient d\'être créée !'
            );

            return $this->redirectToRoute('playlist_add');
        }

        // Récupération des animateurs depuis la table 'playlist', pour ensuite les dédoublonner et les renvoyer vers le front
        $playlists = $this->em->getRepository(Playlist::class)->findAll();
        $animatorsAll = [];
        foreach ($playlists as $playlist) {
            array_push($animatorsAll, $playlist->getAnimator());
        }
        $animators = array_unique($animatorsAll, SORT_REGULAR);

        return $this->render('playlist/add.html.twig', [
            'animators' => $animators
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

            $inventoryNum = $request->query->get('disc');

            if($inventoryNum !== "")
            {
                $disc = $this->em->getRepository(Disc::class)->findOneBy([
                    'num_inventory' => $inventoryNum
                ]);
    
                if($disc)
                {
                    $relation = new PlaylistHasDisc;
    
                    $relation->setDisc($disc)
                             ->setPlaylist($playlist);
                    
                    $playlist->addPlaylistHasDisc($relation);
    
                    $this->addFlash(
                        'add_disc_to_existing_playlist_success',
                        'Bibopalulla ! Le track a été ajouté à la playliste.'
                    );
                }
                else
                {
                    $this->addFlash(
                        'add_disc_to_existing_playlist_alert',
                        'Oups ! Ce track n\'existe pas.'
                    );
                }
    
                $this->em->persist($playlist);
                $this->em->flush();
            }
            else 
            {
                $this->addFlash(
                    'add_disc_to_existing_playlist_alert',
                    'Oups ! Ce track n\'existe pas.'
                );
            }
        }

        return $this->redirectToRoute('show_playlist', ['id' => $playlist->getId()]);
    }

    /**
     * @Route("/playlist/delete/{id}", name="delete_disc_playlist")
     */
    public function deleteDisc(Request $request, PlaylistHasDisc $id): Response
    {
        // dd($id);
        
        $this->em->remove($id);
        $this->em->flush();

        $this->addFlash(
            'delete_track_success',
            'Rock\'n Roll ! Le titre a été retiré de la playliste.'
        );

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/playlist/request_disc/{numero}", name="request_disc")
     */
    public function requestDisc($numero)
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
