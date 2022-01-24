<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Translation;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/course", name="course_list")
     */
    public function index(CourseRepository $courseRepository): Response
    {
        $user = $this->getUser()->getId();
        return $this->render('course/index.html.twig', [
            'course' => $courseRepository->findBy(['user' => $user])
        ]);
    }

    /**
     * @Route("/course/new", name="new_course")
     */
    public function edit(Request $request)
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        $user = $this->getUser();

        if($form->isSubmitted() && $form->isSubmitted()){
            $course->setUser($user);
            $this->em->persist($course);
            $this->em->flush();

            return $this->redirectToRoute('course_list');
        }
        return $this->render('course/form.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
