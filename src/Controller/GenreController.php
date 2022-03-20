<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenreController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/genre", name="genre")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {

        $genres = $this->em->getRepository(Genre::class)->findAll();

        return $this->render('genre/index.html.twig', [
            'genres' => $genres,
        ]);
    }

    /**
     * @Route("/genre/add", name="add_genre")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function addGenre(Request $request): Response
    {
        $genre = new Genre;
        $addGenreType = $this->createForm(GenreType::class, $genre);

        $addGenreType->handleRequest($request);

        if ($addGenreType->isSubmitted() && $addGenreType->isValid()) {
            $genre = $addGenreType->getData();

            $this->em->persist($genre);
            $this->em->flush();

            return $this->redirectToRoute('genre');
        }

        return $this->render('genre/add.html.twig', [
            'addGenreType' => $addGenreType->createView()
        ]);
    }

    /**
     * @Route("/genre/modify/{id}", name="modify_genre")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function modifyGenre(Genre $genre, Request $request): Response
    {
        $modifyGenreForm = $this->createForm(GenreType::class, $genre);

        $modifyGenreForm->handleRequest($request);

        if ($modifyGenreForm->isSubmitted() && $modifyGenreForm->isValid()) {
            $type = $modifyGenreForm->getData();

            $this->em->persist($genre);
            $this->em->flush();

            return $this->redirectToRoute('genre');
        }

        return $this->render('genre/modify.html.twig', [
            'modifyGenreForm' => $modifyGenreForm->createView()
        ]);
    }

    /**
     * @Route("/genre/delete/{id}", name="delete_genre")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteGenre(Genre $id)
    {
        $this->em->remove($id);
        $this->em->flush();

        return $this->redirectToRoute('genre');
    }
}
