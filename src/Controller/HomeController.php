<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Form\DiscType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/home", name="home")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(): Response
    {
        if($this->getUser()->getRoles() === 'ROLE_BENEVOLE')
        {
            return $this->redirectToRoute('playlist_add');
        }
        else 
        {
            return $this->redirectToRoute('add_disc');
        }
    }
}
