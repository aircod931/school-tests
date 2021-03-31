<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\AddUserFormType;
use App\Form\ChangeUserGroupFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\ChangeUserGroup;

class UserManagementController extends AbstractController
{

    /**
     * @Route("/admin/users", name="admin_users")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user = new User();
        $form_AddUser = $this->createForm(AddUserFormType::class, $user);
        $form_AddUser->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form_AddUser->isSubmitted() && $form_AddUser->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form_AddUser->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect('/admin/users?addUser=success');
        }


        $changeUserGroupModel = new ChangeUserGroup();
        $form_ChangeUserGroup = $this->createForm(ChangeUserGroupFormType::class, $changeUserGroupModel);
        $form_ChangeUserGroup->handleRequest($request);
        if ($form_ChangeUserGroup->isSubmitted() && $form_ChangeUserGroup->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()
                ->getRepository('App:User')
                ->findBy(array('id' => $form_ChangeUserGroup->get('username')->getData()));
            $group = $form_ChangeUserGroup['name']->getData();
           $user[0]->setSchoolGroup($group->getID());

            $entityManager->persist($user[0]);
            $entityManager->flush();
            return $this->redirect('/admin/users?changeGroup=success');
        }

        return $this->render('settings/users.twig', [
            'addUserForm'=>$form_AddUser->createView(),
            'changeUserGroupForm'=>$form_ChangeUserGroup->createView(),
        ]);
    }
}