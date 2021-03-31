<?php
namespace App\Controller;
use App\Entity\ChangePassword;
use App\Entity\ChangeUsername;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ChangeUsernameFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SettingsController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="admin_settings")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $changePasswordModel = new ChangePassword();
        $form_changePassword = $this->createForm(ChangePasswordFormType::class, $changePasswordModel);

        $form_changePassword->handleRequest($request);
        if($form_changePassword->isSubmitted() && $form_changePassword->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->find(User::class, $this->getUser()->getId());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form_changePassword->get('newPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect('/admin/settings?changePassword=success');
        }

        $changeUsernameModel = new ChangeUsername();
        $form_changeUsername = $this->createForm(ChangeUsernameFormType::class, $changeUsernameModel);
        $form_changeUsername->handleRequest($request);
        if($form_changeUsername->isSubmitted() && $form_changeUsername->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->find(User::class, $this->getUser()->getId());
            $user->setUsername( $form_changeUsername->get('newUsername')->getData());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect('/admin/settings?changeUsername=success');
        }

        return $this->render('settings/settings.twig', [
            'changeUsernameForm'=>$form_changeUsername->createView(),
            'changePasswordForm'=>$form_changePassword->createView(),
        ]);
    }


}