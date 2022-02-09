<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Form\DiscType;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private DiscRepository $discRepo;
    private EntityManagerInterface $em;

    public function __construct(DiscRepository $discRepo, EntityManagerInterface $em)
    {
        $this->discRepo = $discRepo;
        $this->em = $em;
    }

    /**
     * @Route("/home", name="home")
     * @Security("is_granted('ROLE_BENEVOLE') or is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'accès autorisé")
     */
    public function index(Request $request): Response
    {
        $disc = new Disc();
        $numInventory = $this->discRepo->generateNumInventory();
        $addDiscForm = $this->createForm(DiscType::class, $disc, ['method' => 'POST']);
        $addDiscForm->handleRequest($request);
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $addDiscForm->createView(),
            'num_inventory' => $numInventory
        ]);
    }
}
