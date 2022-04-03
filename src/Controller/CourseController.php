<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Translation;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CourseController extends AbstractController
{
    private $em;
    private $course;
    private $doctrine;

    public function __construct(EntityManagerInterface $em, CourseRepository $course, ManagerRegistry $doctrine)
    {
        $this->em = $em;
        $this->course = $course;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/course", name="course_list")
     */
    public function index(CourseRepository $courseRepository): Response
    {
        $user = $this->getUser();
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findUniqueCourse($user),
        ]);
    }

    /**
     * @Route("/course/chosen/{slug}", name="chosen_course", methods={"POST|GET"})
     */
    public function chosenCourse(Request $request): Response
    {
        $slug = $request->get('slug');
        $user = $this->getUser();
        $course = $this->getRepetition($slug, $user);
        return $this->render('course/course_list.html.twig', [
            'courses' => $this->course->findUniqueCourse($user),
            'courseTypes' => $course['type'],
            'repetitionLength' => $course['length']
        ]);
    }

    /**
     * @Route("/course/{slug}/presentation", name="presentation_course")
     */
    public function presentationCourse(\Doctrine\Persistence\ManagerRegistry $registry, Request $request): Response
    {
        $translation = $registry->getRepository(Translation::class)->findOneBy(['course' => $request->get('slug'), 'repetition' => '0']);
        return $this->render('presentation/slideshow.html.twig', [
            'cards' => $translation,
            'courseId' => $request->get('slug')
        ]);
    }

    /**
     * @Route("/course/{slug}/repetition", name="repetition_course")
     */
    public function repetitionCourse(\Doctrine\Persistence\ManagerRegistry $registry, Request $request): Response
    {
        $slug = $request->get('slug');
        $course = $registry->getRepository(Course::class)->findOneBy(['id' => $slug]);
        $type = $course->getLanguage();
        $cards = $this->getRepetition($type, $this->getUser());
        dump($cards);
        return $this->render('presentation/slideshow.html.twig', [
            'cards' => $cards['cards'][0],
            'courseId' => $request->get('slug')
        ]);
    }

    /**
     * @Route("/course/flashcards", name="course_flashcards", methods={"POST"})
     */
    public function getFlashcards(\Doctrine\Persistence\ManagerRegistry $registry, Request $request, SerializerInterface $serializer): Response
    {
        $date = new \DateTime('now');
        $content = json_decode($request->getContent());
        $newCourse = $content->newCourse;
        $courseId = $content->courseId;
        $id = $content->id;
        $repetition = $content->repetition;
        $lastRepetition = $content->lastRepetition;
        if($newCourse){
            $translations = $registry->getRepository(Translation::class)->findBy(['course' => $courseId, 'repetition' => '0']);
        }else{
            $translations = $registry->getRepository(Translation::class)->findBy(['course' => $courseId, 'nextRepetition' => $date]);
        }
        if ($repetition !== null) {
            $translation = $registry->getRepository(Translation::class)->find($id);
            $translation->setRepetition($repetition);
            $date = new \DateTime($repetition . ' days');
            $translation->setNextRepetition($date);
            $this->em->persist($translation);
            $this->em->flush();
        }
        if (!isset($translations[1])) {
            return new JsonResponse(
                ['message' => 'You did all words for today'], 200);
        }
        $json = $serializer->serialize($translations[1], 'json', ['groups' => 'show_flashcard']);
        return new JsonResponse($json, 200);
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
            if ($form->get('reverse')->getData()) {
                $reverseCourse = new Course();
                $reverseCourse->setUser($user);
                $reverseCourse->setName($form->get('name')->getData() . ' reverse');
                $reverseCourse->setLanguage($form->get('language')->getData());
                foreach ($form->get('translations')->getData() as $card) {
                    $translation = new Translation();
                    $translation->setCourse($reverseCourse);
                    $translation->setFrontSide($card->getBackSide());
                    $translation->setBackSide($card->getFrontSide());
                    $this->em->persist($translation);
                }
                $this->em->persist($reverseCourse);
            }
            $this->em->persist($course);
            $this->em->flush();

            return $this->redirectToRoute('course_list');
        }
        return $this->render('course/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/course/repetition", name="course_repetition", methods={"POST"})
     */
    public function getCourse(\Doctrine\Persistence\ManagerRegistry $registry, Request $request, SerializerInterface $serializer): Response
    {

        $content = json_decode($request->getContent());
        $courseId = $content->courseId;
        $id = $content->id;
        $repetition = $content->repetition;
        $translations = $registry->getRepository(Translation::class)->findBy(['course' => $courseId, 'repetition' => '0']);
        if ($repetition !== null) {
            $translation = $registry->getRepository(Translation::class)->find($id);
            $translation->setRepetition($repetition);
            $date = new \DateTime($repetition . ' days');
            $translation->setNextRepetition($date);
            $this->em->persist($translation);
            $this->em->flush();
        }
        if (!isset($translations[1])) {
            return new JsonResponse(
                ['message' => 'You did all words for today'], 200);
        }

        $json = $serializer->serialize($translations[1], 'json', ['groups' => 'show_flashcard']);
        return new JsonResponse($json, 200);
    }

    public function getRepetition($slug, $user)
    {
        $courseTypes = $this->course->findBy(['language' => $slug, 'user' => $user]);
        $date = new \DateTime();
        $date->add(new \DateInterval('PT2H'));
        $count = 0;
        $countRepetition = [];
        $newWords = [];
        $cardsRepeat = [];
        foreach ($courseTypes as $key => $type) {
            $translations = $this->doctrine->getRepository(Translation::class)->findBy(['course' => $courseTypes[$key]->getId()]);
            $newCards = $this->doctrine->getRepository(Translation::class)->findBy(['course' => $courseTypes[$key]->getId(), 'repetition' => '0']);
            foreach ($translations as $translation) {
                if ($translation->getNextRepetition() && str_contains($date->format('d/M/Y'), $translation->getNextRepetition()->format('d/M/Y'))) {
                    $count++;
                    $cardsRepeat[] = $translation;
                }
            }
            $countRepetition[$type->getId()] = $count;
            $count = 0;

            foreach ($newCards as $card) {
                $count++;
            }
            $newWords[$type->getId()] = $count;
            $count = 0;
        }

        return ['length' => $countRepetition, 'type' => $courseTypes, 'cards' => $cardsRepeat];

    }

}
