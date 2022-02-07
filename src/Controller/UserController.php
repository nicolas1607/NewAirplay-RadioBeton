<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/add", name="add_user")
     */
    public function addUser(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User;
        $addUserForm = $this->createForm(UserType::class, $user);

        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();

            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/add.html.twig', [
            'add_user_form' => $addUserForm->createView()
        ]);
    }

    /**
     * @Route("/user/modify/{id}", name="modify_user")
     */
    public function modifyUser(User $user, Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $modifyUserForm = $this->createForm(UserType::class, $user);

        $modifyUserForm->handleRequest($request);

        if ($modifyUserForm->isSubmitted() && $modifyUserForm->isValid()) {
            $user = $modifyUserForm->getData();

            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/modify.html.twig', [
            'modify_user_form' => $modifyUserForm->createView()
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="delete_user")
     */
    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
