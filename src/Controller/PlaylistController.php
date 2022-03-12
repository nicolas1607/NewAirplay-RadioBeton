<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Entity\User;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Entity\PlaylistHasDisc;
use App\Form\SearchPlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {
        return $this->render('playlist/index.html.twig', [
            'controller_name' => 'PlaylistController',
        ]);
    }


    /**
     * @Route("/playlist/add", name="playlist_add")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function addPlaylist(Request $request): Response
    {
        $date = $request->query->get('date');
        $name = $request->query->get('title');
        $animator = $request->query->get('name');
        $discs = $request->query->get('discs');

        if($date || $name || $animator || $discs)
        {
            if($discs)
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
                    'success',
                    'Rock\'n Roll ! Une nouvelle playlist vient d\'être créée !'
                );

                return $this->redirectToRoute('playlist_add');
            }
            else
            {
                $this->addFlash(
                    'alert',
                    'Nope ! Il n\'y a aucun disque dans cette playlist.'
                );
            }
        }

        // Récupération des animateurs depuis la table 'playlist', pour ensuite les dédoublonner et les renvoyer vers le front
        $users = $this->em->getRepository(User::class)->findAll();
        $animatorsAll = [];
        foreach ($users as $user) {
            array_push($animatorsAll, $user->getUsername());
        }
        $animators = array_unique($animatorsAll, SORT_REGULAR);


        return $this->render('playlist/add.html.twig', [
            'animators' => $animators
        ]);
    }

    /**
     * @Route("/playlist/search", name="search_playlist")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function search(Request $request): Response
    {
        $searchPlaylistForm = $this->createForm(SearchPlaylistType::class);
        $searchPlaylistForm->handleRequest($request);
        
        if ($searchPlaylistForm->isSubmitted() && $searchPlaylistForm->isValid()) {
            $search = $searchPlaylistForm->getData();

            $playlistsQuery = $this->playlistRepo->search(
                $search->getName(), 
                $search->getAnimator(), 
                $search->getEntryDate(),
                $request->request->get('order-by'), 
                $request->request->get('order')
            );
            
            $parameters = [
                $search->getName() ? $search->getName() : "", 
                $search->getAnimator() ? $search->getAnimator() : "", 
                $search->getEntryDate() ? $search->getEntryDate() : "",
                $request->request->get('order-by') ? $request->request->get('order-by') : "",
                $request->request->get('order') ? $request->request->get('order') : ""
            ];
           
            $limit = 15;
            $page = $request->query->get('page');
            if($page === null){
                $currentPage = 1;
            } else {
                $currentPage = $page;
            }
            $offset = ($currentPage - 1) * $limit;
            $query = $this->em->createQuery($playlistsQuery->getDQL())
                                ->setFirstResult($offset)
                                ->setMaxResults($limit);
            
            $paginator = new Paginator($query, $fetchJoinCollection = false);
            $playlists = [];
            foreach ($paginator as $playlist) {
                array_push($playlists, $playlist);
            }
            return $this->render('playlist/search.html.twig', [
                'searchPlaylistForm' => $searchPlaylistForm->createView(),
                'playlists' => $playlists,
                'totalPages' => ceil($paginator->count() / $limit),
                'currentPage' => $currentPage,
                'issues' => $paginator->getIterator(),
                'parameters' => $parameters,
                'count' => $paginator->count()
            ]);
        }
        
        if($request->query->get('page') && $request->query->get('parameters'))
        {
            $parameters = $request->query->get('parameters');
            
            $name = $parameters[0];
            $album = $parameters[1];
            $entryDate = $parameters[2];
            $orderBy = $parameters[3];
            $order = $parameters[4];

            $playlistsQuery = $this->playlistRepo->search($name, $album, $entryDate, $orderBy, $order);

            $limit = 15;
            $page = $request->query->get('page');
            if($page === null){
                $currentPage = 1;
            } else {
                $currentPage = $page;
            }
            $offset = ($currentPage - 1) * $limit;
            $query = $this->em->createQuery($playlistsQuery->getDQL())
                                ->setFirstResult($offset)
                                ->setMaxResults($limit);

            $paginator = new Paginator($query, $fetchJoinCollection = false);
            $playlists = [];
            foreach ($paginator as $playlist) {
                array_push($playlists, $playlist);
            }

            return $this->render('playlist/search.html.twig', [
                'searchPlaylistForm' => $searchPlaylistForm->createView(),
                'playlists' => $playlists,
                'totalPages' => ceil($paginator->count() / $limit),
                'currentPage' => $currentPage,
                'issues' => $paginator->getIterator(),
                'parameters' => $parameters,
                'count' => $paginator->count()
            ]);
        }
        
        return $this->render('playlist/search.html.twig', [
            'searchPlaylistForm' => $searchPlaylistForm->createView(),
            'playlists' => null,
            'totalPages' => null,
            'currentPage' => null,
            'issues' => null,
            'parameters' => [],
            'count' => null
        ]);
    }

    /**
     * @Route("/playlist/show/{id}", name="show_playlist")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    /**
     * @Route("/playlist/add/disc/{playlist}", name="add_disc_playlist")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
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
                        'success',
                        'Bibopalulla ! Le track a été ajouté à la playliste.'
                    );
                }
                else
                {
                    $this->addFlash(
                        'alert',
                        'Oups ! Ce track n\'existe pas.'
                    );
                }
    
                $this->em->persist($playlist);
                $this->em->flush();
            }
            else 
            {
                $this->addFlash(
                    'alert',
                    'Oups ! Ce track n\'existe pas.'
                );
            }
        }

        return $this->redirectToRoute('show_playlist', ['id' => $playlist->getId()]);
    }

    /**
     * @Route("/playlist/delete/disc/{id}", name="delete_disc_playlist")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteDisc(Request $request, PlaylistHasDisc $id): Response
    {
        $this->em->remove($id);
        $this->em->flush();
        
        $this->addFlash(
            'success',
            'Rock\'n Roll ! Le titre a été retiré de la playliste.'
        );

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/playlist/delete/{id}", name="delete_playlist")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     * @param Playlist $playlist
     * @param Request $request
     * @return void
     */
    public function deletePlaylist(Playlist $playlist, Request $request)
    {
        if($playlist)
        {
            $this->em->remove($playlist);
            $this->em->flush();
            
            $this->addFlash(
                'success',
                'Rock\'n Roll ! La playlist a été effacée.'
            );
        }
        else 
        {
            $this->addFlash(
                'alert',
                'Ha ? Il y a eu un problème...'
            );
        }

        $searchPlaylistForm = $this->createForm(SearchPlaylistType::class);
        $searchPlaylistForm->handleRequest($request);

        $parameters = $request->query->get('parameters');
            
        $name = $parameters[0];
        $album = $parameters[1];
        $entryDate = $parameters[2];
        $orderBy = $parameters[3];
        $order = $parameters[4];

        $playlistsQuery = $this->playlistRepo->search($name, $album, $entryDate, $orderBy,$order);

        $limit = 15;
        $page = $request->query->get('page');
        if($page === null){
            $currentPage = 1;
        } else {
            $currentPage = $page;
        }
        $offset = ($currentPage - 1) * $limit;
        $query = $this->em->createQuery($playlistsQuery->getDQL())
                            ->setFirstResult($offset)
                            ->setMaxResults($limit);

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $playlists = [];
        foreach ($paginator as $playlist) {
            array_push($playlists, $playlist);
        }

        return $this->render('playlist/search.html.twig', [
            'searchPlaylistForm' => $searchPlaylistForm->createView(),
            'playlists' => $playlists,
            'totalPages' => ceil($paginator->count() / $limit),
            'currentPage' => $currentPage,
            'issues' => $paginator->getIterator(),
            'parameters' => $parameters,
            'count' => $paginator->count()
        ]);
    }

    /**
     * @Route("/playlist/request_disc/{numero}", options={"expose"=true}, name="request_disc")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
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

        // $discs = $this->em->getRepository(Disc::class)->searchDiscs($numero);
        
        // $results = [];
        // foreach($discs as $disc)
        // {
        //     $result = [
        //         'id' => $disc->getId(),
        //         'group' => $disc->getGroupe(),
        //         'album' => $disc->getAlbum(),
        //         'inventory_num' => $disc->getNumInventory()
        //     ];

        //     array_push($results, $result);
        // }

        // $response = new JsonResponse($results);
        
        if($response)
        {
            return $response;
        }
    }
}
