<?php

namespace App\Controller;

use App\Entity\LeaveReason;
use App\Form\LeaveReasonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeaveReasonController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/leaveReason", name="leave_reason")
     */
    public function index(): Response
    {
        $leaveReasons = $this->em->getRepository(LeaveReason::class)->findAll();

        return $this->render('leave_reason/index.html.twig', [
            'leaveReasons' => $leaveReasons,
        ]);
    }

    /**
     * @Route("/leaveReason/add", name="add_leaveReason")
     */
    public function addLeaveReason(Request $request): Response
    {
        $leaveReason = new LeaveReason;
        $addleaveReasonType = $this->createForm(LeaveReasonType::class, $leaveReason);

        $addleaveReasonType->handleRequest($request);

        if ($addleaveReasonType->isSubmitted() && $addleaveReasonType->isValid()) {
            $leaveReason = $addleaveReasonType->getData();

            $this->em->persist($leaveReason);
            $this->em->flush();

            return $this->redirectToRoute('leave_reason');
        }

        return $this->render('leave_reason/add.html.twig', [
            'addleaveReasonType' => $addleaveReasonType->createView()
        ]);
    }

    /**
     * @Route("/leaveReason/modify/{id}", name="modify_leaveReason")
     */
    public function modifyGenre(LeaveReason $leaveReason, Request $request): Response
    {
        $modifyLeaveReasonForm = $this->createForm(LeaveReasonType::class, $leaveReason);

        $modifyLeaveReasonForm->handleRequest($request);

        if ($modifyLeaveReasonForm->isSubmitted() && $modifyLeaveReasonForm->isValid()) {
            $type = $modifyLeaveReasonForm->getData();

            $this->em->persist($leaveReason);
            $this->em->flush();

            return $this->redirectToRoute('leave_reason');
        }

        return $this->render('leave_reason/modify.html.twig', [
            'modifyLeaveReasonForm' => $modifyLeaveReasonForm->createView()
        ]);
    }

    /**
     * @Route("/leaveReason/delete/{id}", name="delete_leaveReason")
     */
    public function deleteLeaveReason(LeaveReason $id)
    {
        $this->em->remove($id);
        $this->em->flush();

        return $this->redirectToRoute('leave_reason');
    }
}
