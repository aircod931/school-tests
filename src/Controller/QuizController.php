<?php


namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizQuestionOption;
use App\Entity\QuizUserAnswer;
use App\Entity\User;
use App\Entity\UserQuestionAnswer;
use App\Form\AddQuestionFormType;
use App\Form\AddQuestionOptionFormType;
use App\Form\AddQuizFormType;
use App\Form\QuizFormType;
use App\Form\ShowQuizFormType;
use App\Form\StartQuizFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class QuizController extends AbstractController
{
    /**
     * @Route("/admin/quiz", name="admin_quiz")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $quiz = new Quiz();
        $form_addQuiz = $this->createForm(AddQuizFormType::class);
        $form_addQuiz->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form_addQuiz->isSubmitted() && $form_addQuiz->isValid()) {
            $quiz->setTitle($form_addQuiz->get('title')->getData());
            $entityManager->persist($quiz);
            $entityManager->flush();
            return $this->redirect('/admin/quiz?addQuiz=success');
        }

        $quiz_question = new QuizQuestion();
        $form_addQuizQuestion = $this->createForm(AddQuestionFormType::class);
        $form_addQuizQuestion->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form_addQuizQuestion->isSubmitted() && $form_addQuizQuestion->isValid()) {
            $quiz_question->setText($form_addQuizQuestion->get('text')->getData());
            $quiz_Object = $form_addQuizQuestion->get('quiz_id')->getData();
            $quiz_question->setQuizId($quiz_Object);
            $entityManager->persist($quiz_question);
            $entityManager->flush();
            return $this->redirect('/admin/quiz?addQuizQuestion=success');
        }

        $quiz_questionOption = new QuizQuestionOption();
        $form_addQuizQuestionOption = $this->createForm(AddQuestionOptionFormType::class);
        $form_addQuizQuestionOption->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if (  $form_addQuizQuestionOption->isSubmitted() &&   $form_addQuizQuestionOption->isValid()) {
            $quiz_questionOption->setText($form_addQuizQuestionOption->get('text')->getData());
            $quizQuestion_Object = $form_addQuizQuestionOption->get('quizQuestion_id')->getData();
            $quiz_questionOption->setText($form_addQuizQuestionOption->get('text')->getData());
            $quiz_questionOption->setIsCorrect($form_addQuizQuestionOption->get('correct')->getData());

            $quiz_questionOption->setQuizQuestion($quizQuestion_Object);
            $entityManager->persist($quiz_questionOption);
            $entityManager->flush();
            return $this->redirect('/admin/quiz?addQuizQuestionOption=success');
        }

        return $this->render('settings/quiz.twig', [
            'addQuizForm'=>$form_addQuiz->createView(),
            'addQuizQuestionForm'=>$form_addQuizQuestion->createView(),
            'addQuizQuestionOption' =>  $form_addQuizQuestionOption->createView()
        ]);
    }

    /**
     * @Route("/user/quiz", name="user_quiz")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function getUserQuiz(){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $currentUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());


        $quiz_object = $this->getDoctrine()
            ->getRepository(Quiz::class)
            ->find($currentUser->getSchoolGroup());

        return $this->render('user/quiz.twig', array('quiz_title'=>$quiz_object->getTitle()));

    }



    /**
     * @Route("/user/quiz/{id}", name="user_startQuiz")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function generateQuizHomepage($id, Request $request){
        $session = new Session();
        $entityManager = $this->getDoctrine()->getManager();
        $quiz = $entityManager->getRepository(Quiz::class)->find($id);
        $question_array = $quiz->getQuizQuestions()->toArray();
        shuffle($question_array);
        $answers_Array = array();

        for($i=0; $i<count($quiz->getQuizQuestions()->toArray()); $i++){
            array_push($answers_Array,$question_array[$i]->getQuizQuestionOptions()->toArray());
        }

        $user = $this->getUser();
        $currentUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());

        $quiz_object = $this->getDoctrine()
            ->getRepository(Quiz::class)
            ->find($currentUser->getSchoolGroup());

        $startQuizForm = $this->createForm(StartQuizFormType::class);
        $startQuizForm->handleRequest($request);

        if($startQuizForm->isSubmitted()) {
            $session->set('answers_Array', $answers_Array);
            $session->set('answers_Question', $question_array);
            return $this->redirect('/user/quiz/'.$id.'/1');
        }

        return $this->render('user/startQuiz.twig', [
            'startQuizForm'=>$startQuizForm->createView(),
           'quiz_title'=>$quiz_object->getTitle(),
        ]);
    }

    /**
     * @Route("/user/quiz/{id}/{id_question}", name="user_showQuiz")
     * @param $id
     * @param $id_question
     * @param Request $request
     * @return Response
     */
    public function showUserQuiz($id, $id_question, Request $request){
        $session = new Session();
        $question_array = $session->get('answers_Question'); //Array with generated questions
        $questionArray = $session->get('answers_Array')[$id_question]; //Array with generated answers

        $formOptions = array(
            'questions' => $questionArray,
        );

        $form = $this->createForm(QuizFormType::class, $question_array[$id_question], $formOptions);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $choice = $form->get('text')->getData();
        }

        return $this->render('user/showQuiz.twig', [
            'quizForm'=>$form->createView(),
            'questions' => $question_array[$id_question],
            'test2' => $session->get('answers_Array'),
        ]);
    }
}