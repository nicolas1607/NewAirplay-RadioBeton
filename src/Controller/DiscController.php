<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Form\DiscType;
use App\Form\SearchDiscType;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function add(Request $request): Response
    {
        $disc = new Disc();
        $numInventory = $this->discRepo->generateNumInventory();
        $addDiscForm = $this->createForm(DiscType::class, $disc, ['method' => 'POST']);
        $addDiscForm->handleRequest($request);

        if ($addDiscForm->isSubmitted() && $addDiscForm->isValid()) {
            $disc = $addDiscForm->getData();
            $disc->setNumInventory($numInventory);

            $this->em->persist($disc);
            $this->em->flush();

            return $this->redirectToRoute('add_disc');
        }

        return $this->render('disc/add.html.twig', [
            'add_disc_form' => $addDiscForm->createView(),
            'num_inventory' => $numInventory

        ]);
    }

    /**
     * @Route("/disc/search", name="search_disc")
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function search(Request $request): Response
    {

        $searchDiscForm = $this->createForm(SearchDiscType::class);
        $searchDiscForm->handleRequest($request);

        if ($searchDiscForm->isSubmitted() && $searchDiscForm->isValid()) {
            $search = $searchDiscForm->getData();
            $discs = $this->discRepo->search($search->getNumInventory(), $search->getAlbum(), $search->getGroupe());

            return $this->render('disc/search.html.twig', [
                'searchDiscForm' => $searchDiscForm->createView(),
                'discs' => $discs
            ]);
        }

        return $this->render('disc/search.html.twig', [
            'searchDiscForm' => $searchDiscForm->createView(),
            'discs' => null
        ]);
    }

    /**
     * @Route("/disc/edit/{id}", name="edit_disc")
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function edit(Request $request, Disc $id): Response
    {
        $updateDiscForm = $this->createForm(DiscType::class, $id);
        $updateDiscForm->handleRequest($request);

        if ($updateDiscForm->isSubmitted() && $updateDiscForm->isValid()) {
            $this->em->flush();
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('disc/edit.html.twig', [
            'edit_disc_form' => $updateDiscForm->createView(),
            'num_inventory' => $id->getNumInventory()
        ]);
    }

    /**
     * @Route("/disc/delete/{id}", name="delete_disc")
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function delete(Request $request, Disc $disc): Response
    {
        $this->em->remove($disc);
        $this->em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
