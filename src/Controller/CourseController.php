<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\LearningPlan;
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
        $news = json_decode(file_get_contents( 'https://newsdata.io/api/1/news?apikey=pub_7950d38fade3166f8f4966b5f6f725a8f6c8&q=technology&language=en&category=technology'));
        $user = $this->getUser();
        $course = $this->getRepetition(null,$user);

        if(count($courseRepository->findAll()) < 1){
            return $this->redirectToRoute('new_course');
        }

        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findUniqueCourse($user),
            'courseTypes' => $course['type'],
            'repetitionLength' => $course['length'],
            'news' => $news
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
        if($cards['length'][$slug] < 1){
            return $this->redirectToRoute('course_list');
        }
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
        $content = json_decode($request->getContent());
        $newCourse = $content->newCourse;
        $courseId = $content->courseId;
        $id = $content->id;
        $repetition = $content->repetition;
        $message = 'You did all words for today';
        $date = new \DateTime('now');
        $learningPlan = $registry->getRepository(LearningPlan::class)->findOneBy(['course' => $courseId]);
        if($learningPlan->getUpdatedAt()->format('Y-m-d') !== $date->format('Y-m-d')){
            $countCards = 0;
        }else{
            $countCards = $learningPlan->getCurrentCardLearnt() + 1;
        }

        $translation = $registry->getRepository(Translation::class)->find($id);
        $translation->setRepetition($repetition);
        $translation->setNextRepetition(new \DateTime($repetition . ' days'));
        $this->em->persist($translation);

        if ($learningPlan->getCurrentCardLearnt() == null || $countCards <= $learningPlan->getLimitOnDay() || $countCards >= $learningPlan->getLimitOnDay()) {
            $learningPlan->setCurrentCardLearnt($countCards);
            $learningPlan->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($learningPlan);
        }
        $this->em->flush();

        if ($learningPlan->getCurrentCardLearnt() === $learningPlan->getLimitOnDay()) {
            return new JsonResponse(['message' => $message], 200);
        }

        if($newCourse){
            $translations = $registry->getRepository(Translation::class)->findBy(['course' => $courseId, 'repetition' => '0']);
        }else{
            $translations = $registry->getRepository(Translation::class)->findRepetition($courseId);
        }

        if (!isset($translations[0])) {
            return new JsonResponse(['message' => $message], 200);
        }

        $json = $serializer->serialize($translations[0], 'json', ['groups' => 'show_flashcard']);
        return new JsonResponse($json, 200);
    }

    /**
     * @Route("/course/new", name="new_course")
     * @Route("/course/edit/{id}", name="edit_course")
     */
    public function edit(Request $request, $id = null)
    {
        $course = new Course();

        if($id !== null){
            $course = $this->doctrine->getRepository(Course::class)->find($id);
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isSubmitted()) {
            $course->setUser($user);
            if($id === null){
                $plan = new LearningPlan();
                $plan->setCourse($course);
                $plan->setLimitOnDay(10);
                $plan->setCreatedAt(new \DateTime('now'));
                $plan->setUpdatedAt(new \DateTime('now'));
                $plan->setCurrentCardLearnt(0);
                $this->em->persist($plan);
            }

            if ($form->get('reverse')->getData()) {
                if($id ===null){
                    $reverseCourse = new Course();
                }else{
                    $reverseCourse = $this->doctrine->getRepository(Course::class)->findOneBy(['name' => $course->getName() . ' reverse']);
                }
                $reverseCourse->setUser($user);
                $reverseCourse->setName($form->get('name')->getData() . ' reverse');
                $reverseCourse->setLanguage($form->get('language')->getData() . ' reverse');
                $reverseCourse->setReverse(0);
                foreach ($form->get('translations')->getData() as $card) {
                    $translation = new Translation();
                    $translation->setCourse($reverseCourse);
                    $translation->setFrontSide($card->getBackSide());
                    $translation->setBackSide($card->getFrontSide());
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
            'form' => $form->createView(),
            'course' => $course
        ]);
    }



    public function getRepetition($slug, $user)
    {
        if($slug !== null){
            $courseTypes = $this->course->findBy(['language' => $slug, 'user' => $user]);
        }else{
            $courseTypes = $this->course->findBy([ 'user' => $user]);
        }
        $date = new \DateTime();
        $date->add(new \DateInterval('PT2H'));
        $countRepetition = [];
        $newWords = [];
        $cardsRepeat = [];
        foreach ($courseTypes as $type) {
            $translations = $this->doctrine->getRepository(Translation::class)->findRepetition($type->getId());
            $newCards = $this->doctrine->getRepository(Translation::class)->findBy(['course' => $type->getId(), 'repetition' => '0']);
            foreach ($translations as $translation) {
                    $cardsRepeat[] = $translation;
            }
//            dd($type, $courseTypes[$key], $type);
            $countRepetition[$type->getId()] = count($translations);


            $newWords[$type->getId()] = count($newCards);
        }

        return ['length' => $countRepetition, 'type' => $courseTypes, 'cards' => $cardsRepeat];

    }

    /**
     * @Route("/course/delete/{id}", name="remove_course", methods={"POST"})
     */
    public function removeCourse(Request $request, Course $course){
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->doctrine->getManager();
            $em->remove($course);
            $em->flush();
            return $this->redirectToRoute('course_list');
        }
    }
}
