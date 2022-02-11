<?php

namespace App\Controller;

use App\Entity\Nationality;
use App\Form\NationalityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NationalityController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/nationality", name="nationality")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {
        $nationalities = $this->em->getRepository(Nationality::class)->findAll();
        
        return $this->render('nationality/index.html.twig', [
            'nationalities' => $nationalities,
        ]);
    }

    /**
     * @Route("/nationality/add", name="add_nationality")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function addNationality(Request $request): Response 
    {
        $nationality = new Nationality;
        $addNationalityForm = $this->createForm(NationalityType::class, $nationality);

        $addNationalityForm->handleRequest($request);

        if($addNationalityForm->isSubmitted() && $addNationalityForm->isValid())
        {
            $nationality = $addNationalityForm->getData();

            $this->em->persist($nationality);
            $this->em->flush();

            return $this->redirectToRoute('nationality');
        }

        return $this->render('nationality/add.html.twig', [
            'add_nationality_form' => $addNationalityForm->createView()
        ]);
    }

    /**
     * @Route("/nationality/modify/{id}", name="modify_nationality")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function modifyNationality(Nationality $nationality, Request $request): Response 
    {
        $modifyNationalityForm = $this->createForm(NationalityType::class, $nationality);

        $modifyNationalityForm->handleRequest($request);

        if($modifyNationalityForm->isSubmitted() && $modifyNationalityForm->isValid())
        {
            $nationality = $modifyNationalityForm->getData();

            $this->em->persist($nationality);
            $this->em->flush();

            return $this->redirectToRoute('nationality');
        }

        return $this->render('nationality/modify.html.twig', [
            'modify_nationality_form' => $modifyNationalityForm->createView()
        ]);
    }

    /**
     * @Route("/nationality/delete/{id}", name="delete_nationality")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteNationality(Nationality $nationality): Response 
    {
        $this->em->remove($nationality);
        $this->em->flush();
    }
}
