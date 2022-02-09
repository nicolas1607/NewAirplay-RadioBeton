<?php

namespace App\Controller;

use App\Entity\Disc;
use App\Form\DiscType;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
