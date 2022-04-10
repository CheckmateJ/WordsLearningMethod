<?php

namespace App\Controller;

use App\Entity\LearningPlan;
use App\Form\LearningPlanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LearningPlanController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/course/learning/plan", name="learning_plan_new")
     */
    public function new(Request $request): Response
    {
        $learningPlan = new LearningPlan();

        $form = $this->createForm(LearningPlanType::class, $learningPlan);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($learningPlan);
            $this->em->flush();
        }

        return $this->render('learning_plan/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
