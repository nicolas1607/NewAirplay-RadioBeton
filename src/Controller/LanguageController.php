<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/language", name="language")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {
        $languages = $this->em->getRepository(Language::class)->findAll();
        
        return $this->render('nationality/index.html.twig', [
            'languages' => $languages,
        ]);
    }

    /**
     * @Route("/language/add", name="add_language")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function addLanguage(Request $request): Response 
    {
        $language = new Language;
        $addLanguageForm = $this->createForm(LanguageType::class, $nationality);

        $addLanguageForm->handleRequest($request);

        if($addLanguageForm->isSubmitted() && $addLanguageForm->isValid())
        {
            $language = $addLanguageForm->getData();

            $this->em->persist($language);
            $this->em->flush();

            return $this->redirectToRoute('language');
        }

        return $this->render('language/add.html.twig', [
            'add_language_form' => $addLanguageForm->createView()
        ]);
    }

    /**
     * @Route("/language/modify/{id}", name="modify_language")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function modifyLanguage(Language $language, Request $request): Response 
    {
        $modifyLanguageForm = $this->createForm(LanguageType::class, $nationality);

        $modifyLanguageForm->handleRequest($request);

        if($modifyLanguageForm->isSubmitted() && $modifyLanguageForm->isValid())
        {
            $language = $modifyLanguageForm->getData();

            $this->em->persist($language);
            $this->em->flush();

            return $this->redirectToRoute('language');
        }

        return $this->render('language/modify.html.twig', [
            'modify_language_form' => $modifyLanguageForm->createView()
        ]);
    }

    /**
     * @Route("/language/delete/{id}", name="delete_language")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteType(Language $language): Response 
    {
        $this->em->remove($language);
        $this->em->flush();
    }
}
