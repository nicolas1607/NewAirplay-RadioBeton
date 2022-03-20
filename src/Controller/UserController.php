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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user", name="user")
     * @Security("is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
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

            $this->addFlash(
                'success',
                'Yes ! Il y a une nouvelle ou un nouveau dans l\'équipe.'
            );

            return $this->redirectToRoute('add_user');
        }

        return $this->render('user/add.html.twig', [
            'add_user_form' => $addUserForm->createView()
        ]);
    }

    /**
     * @Route("/user/modify/{id}", name="modify_user")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function modifyUser(User $user, Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $modifyUserForm = $this->createForm(UserType::class, $user);

        $modifyUserForm->handleRequest($request);

        if ($modifyUserForm->isSubmitted() && $modifyUserForm->isValid()) {
            $user = $modifyUserForm->getData();

            if ($encoder->needsRehash($user)) {
                $password = $user->getPassword();
                $passwordHashed = $encoder->hashPassword($user, $password);
                $user->setPassword($passwordHashed);
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Voilà ! les modif\' sont faites.'
            );

            return $this->redirectToRoute('user');
        }

        return $this->render('user/modify.html.twig', [
            'modify_user_form' => $modifyUserForm->createView()
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="delete_user")
     * @Security("is_granted('ROLE_SUPERADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function deleteUser(User $user)
    {
        $this->addFlash(
            'success',
            'Utilisateur supprimé !'
        );

        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('user');
    }
}
