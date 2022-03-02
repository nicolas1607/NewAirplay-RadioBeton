<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Form\DiscType;
use App\Form\SearchDiscType;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscController extends AbstractController
{
    private DiscRepository $discRepo;
    private EntityManagerInterface $em;

    public function __construct(DiscRepository $discRepo, EntityManagerInterface $em)
    {
        $this->discRepo = $discRepo;
        $this->em = $em;
    }

    /**
     * @Route("/disc/add", name="add_disc")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function add(Request $request): Response
    {
        $disc = new Disc();
        $numInventory = $this->discRepo->generateNumInventory();
        $addDiscForm = $this->createForm(DiscType::class, $disc, ['method' => 'POST']);
        $addDiscForm->handleRequest($request);

        if ($addDiscForm->isSubmitted() && $addDiscForm->isValid()) {
            $disc = $addDiscForm->getData();
            if ($request->get('numInventory')) {
                $disc->setNumInventory($numInventory);
            } else {
                $disc->setNumInventory(0);
            }

            $this->em->persist($disc);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Youpi ! Un nouveau son à écouter.'
            );

            return $this->redirectToRoute('add_disc');
        }

        return $this->render('disc/add.html.twig', [
            'add_disc_form' => $addDiscForm->createView(),
            'numInventory' => $numInventory
        ]);
    }

    /**
     * @Route("/disc/search", name="search_disc")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function search(Request $request): Response
    {
        $searchDiscForm = $this->createForm(SearchDiscType::class);
        $searchDiscForm->handleRequest($request);

        if ($searchDiscForm->isSubmitted() && $searchDiscForm->isValid()) {
            $search = $searchDiscForm->getData();
            $discsQuery = $this->discRepo->search($search->getNumInventory(), $search->getAlbum(), $search->getGroupe());
            
            $parameters = [
                $search->getNumInventory() ? $search->getNumInventory() : "", 
                $search->getAlbum() ? $search->getAlbum() : "", 
                $search->getGroupe() ? $search->getGroupe() : ""
            ];
            
            $limit = 15;
            $page = $request->query->get('page');
            if($page === null){
                $currentPage = 1;
            } else {
                $currentPage = $page;
            }
            $offset = ($currentPage - 1) * $limit;
            $query = $this->em->createQuery($discsQuery->getDQL())
                                ->setFirstResult($offset)
                                ->setMaxResults($limit);
            
            $paginator = new Paginator($query, $fetchJoinCollection = false);
            $discs = [];
            foreach ($paginator as $disc) {
                array_push($discs, $disc);
            }

            return $this->render('disc/search.html.twig', [
                'searchDiscForm' => $searchDiscForm->createView(),
                'discs' => $discs,
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
            
            $numInventory = $parameters[0];
            $album = $parameters[1];
            $groupe = $parameters[2];

            $discsQuery = $this->discRepo->search($numInventory, $album, $groupe);
            
            $limit = 15;
            $page = $request->query->get('page');
            if($page === null){
                $currentPage = 1;
            } else {
                $currentPage = $page;
            }
            $offset = ($currentPage - 1) * $limit;
            $query = $this->em->createQuery($discsQuery->getDQL())
                                ->setFirstResult($offset)
                                ->setMaxResults($limit);

            $paginator = new Paginator($query, $fetchJoinCollection = false);
            $discs = [];
            foreach ($paginator as $disc) {
                array_push($discs, $disc);
            }

            return $this->render('disc/search.html.twig', [
                'searchDiscForm' => $searchDiscForm->createView(),
                'discs' => $discs,
                'totalPages' => ceil($paginator->count() / $limit),
                'currentPage' => $currentPage,
                'issues' => $paginator->getIterator(),
                'parameters' => $parameters,
                'count' => $paginator->count()
            ]);
        }

        return $this->render('disc/search.html.twig', [
            'searchDiscForm' => $searchDiscForm->createView(),
            'discs' => null,
            'currentPage' => null,
            'totalPages' => null,
            'count' => null,
            'parameters' => []
        ]);
    }

    /**
     * @Route("/disc/edit/{id}", name="edit_disc")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function edit(Request $request, Disc $id): Response
    {
        $updateDiscForm = $this->createForm(DiscType::class, $id);
        $updateDiscForm->handleRequest($request);
        $numInventory = $this->discRepo->generateNumInventory();

        if ($updateDiscForm->isSubmitted() && $updateDiscForm->isValid()) {
            if ($request->get('numInventory') === 0) {
                $id->setNumInventory(0);
            } else {
                $id->setNumInventory($request->get('numInventory'));
            }
            $this->em->flush();
            // return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('disc/edit.html.twig', [
            'numInventory' => $numInventory,
            'disc' => $id,
            'edit_disc_form' => $updateDiscForm->createView(),
        ]);
    }

    /**
     * @Route("/disc/delete/{id}", name="delete_disc")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function delete(Request $request, Disc $disc): Response
    {
        $this->em->remove($disc);
        $this->em->flush();

        $this->addFlash(
            'success',
            'Disque supprimé...'
        );

        return $this->redirect($request->headers->get('referer'));
    }
}
