<?php


namespace App\Controller;
use App\Entity\Group;
use App\Entity\Quiz;
use App\Form\AddGroupFormType;
use App\Form\AddTestToGroupFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GroupManagemenetController extends AbstractController
{
    /**
     * @Route("/admin/groups", name="admin_groups")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $group = new Group();
        $form_addGroup = $this->createForm(AddGroupFormType::class, $group);
        $form_addGroup->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form_addGroup->isSubmitted() && $form_addGroup->isValid()) {
            $group->setName($form_addGroup->get('name')->getData());
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirect('/admin/groups?addGroup=success');
        }


        $form_addTestToGroup = $this->createForm(AddTestToGroupFormType::class);
        $form_addTestToGroup->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form_addTestToGroup->isSubmitted() && $form_addTestToGroup->isValid()) {
            $quizId_object = $form_addTestToGroup->get('quizQuestion')->getData();
            $choosedGroup = $form_addTestToGroup->get('groupName')->getData();
            $newGroup = $entityManager->getRepository(Quiz::class)->find($choosedGroup->getID());
            $choosedGroup->setTest($newGroup);
            $entityManager->persist($choosedGroup);
            $entityManager->flush();
            return $this->redirect('/admin/groups?addTestToGroup=success');
        }

        return $this->render('settings/groups.twig', [
            'addGroupForm'=>$form_addGroup->createView(),
            'addTestToGroupForm'=>$form_addTestToGroup->createView()
        ]);
    }
}