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
        $learningPlans = $this->em->getRepository(LearningPlan::class)->findAll();
        $learning = new LearningPlan();
        $form = $this->createForm(LearningPlanType::class, $learning);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            foreach ($learningPlans as $plan){
                if($request->get('learning_plan_'. $plan->getId())){
                    $plan->setLimitOnDay($request->get('learning_plan_'.$plan->getId()));
                    $this->em->persist($plan);
                }
            }
            $this->em->flush();
        }
        return $this->render('learning_plan/form.html.twig', [
            'learningPlan' => $learningPlans,
            'form' => $form->createView()
        ]);
    }
}
