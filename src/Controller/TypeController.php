<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TypeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/type", name="type")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {
        $types = $this->em->getRepository(Type::class)->findAll();

        return $this->render('type/index.html.twig', [
            'types' => $types,
        ]);
    }

    /**
     * @Route("/type/add/{id}", name="add_type")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function addType(Request $request): Response
    {
        $type = new Type;
        $addTypeForm = $this->createForm(TypeType::class, $type);

        $addTypeForm->handleRequest($request);

        if ($addTypeForm->isSubmitted() && $addTypeForm->isValid()) {
            $type = $addTypeForm->getData();

            $this->em->persist($type);
            $this->em->flush();

            return $this->redirectToRoute('type');
        }

        return $this->render('type/add.html.twig', [
            'add_type_form' => $addTypeForm->createView()
        ]);
    }

    /**
     * @Route("/type/modify/{id}", name="modify_form")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function modifyType(Type $type, Request $request): Response
    {
        $modifyTypeForm = $this->createForm(TypeType::class, $type);

        $modifyTypeForm->handleRequest($request);

        if ($modifyTypeForm->isSubmitted() && $modifyTypeForm->isValid()) {
            $type = $modifyTypeForm->getData();

            $this->em->persist($type);
            $this->em->flush();

            return $this->redirectToRoute('type');
        }

        return $this->render('type/modify.html.twig', [
            'modify_type_form' => $modifyTypeForm->createView()
        ]);
    }

    /**
     * @Route("/type/delete/{id}", name="delete_type")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteType(Type $type): Response
    {
        $this->em->remove($type);
        $this->em->flush();
    }
}
