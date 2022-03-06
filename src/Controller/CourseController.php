<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Translation;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
        $user = $this->getUser();
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findUniqueCourse($user)
        ]);
    }

    /**
     * @Route("/course/chosen/{slug}", name="chosen_course")
     */
    public function chosenCourse(CourseRepository $courseRepository, Request $request): Response
    {
        $user = $this->getUser();
        $slug = $request->get('slug');
        return $this->render('course/course_list.html.twig', [
            'courses' => $courseRepository->findUniqueCourse($user),
            'courseTypes' => $courseRepository->findBy(['language' => $slug, 'user' => $user])
        ]);
    }

    /**
     * @Route("/course/{slug}/presentation", name="presentation_course")
     */
    public function presentationCourse(\Doctrine\Persistence\ManagerRegistry $registry, Request $request): Response
    {
        $translation = $registry->getRepository(Translation::class)->findOneBy(['course' => $request->get('slug'), 'repetition' => '0']);
        return $this->render('presentation/slideshow.html.twig', [
            'translation' => $translation,
            'courseId' => $request->get('slug')
        ]);
    }

    /**
     * @Route("/course/flashcards", name="course_flashcards", methods={"POST"})
     */
    public function getFlashcards(\Doctrine\Persistence\ManagerRegistry $registry, Request $request): Response
    {
        $content = json_decode($request->getContent());
        $courseId = $content->courseId;
        $id = $content->id;
        $repetition = $content->repetition;
        $translations = $registry->getRepository(Translation::class)->findBy(['course' => $courseId, 'repetition' => '0']);
        if ($repetition !== null) {
            $translation = $registry->getRepository(Translation::class)->find($id);
            $translation->setRepetition($repetition);
            $this->em->persist($translation);
            $this->em->flush();
        }


        $words = [];
        foreach ($translations as $translation) {
            $words[$translation->getFrontSide()] = $translation->getBackSide();
        }
        return new JsonResponse($words, 200);
    }

    /**
     * @Route("/course/new", name="new_course")
     */
    public function edit(Request $request)
    {
//        $date = new \DateTime('now');
//        dump($date->add(new \DateInterval('P1D')));
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isSubmitted()) {
            $course->setUser($user);
            $this->em->persist($course);
            $this->em->flush();

            return $this->redirectToRoute('course_list');
        }
        return $this->render('course/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
