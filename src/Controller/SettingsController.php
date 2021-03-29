<?php
namespace App\Controller;
use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
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
     * @Route("/admin/password/change", name="admin_password_change")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordFormType::class, $changePasswordModel);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->find(User::class, $this->getUser()->getId());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect('/admin/password/change?valid=success');
        }


        return $this->render('settings/change-password.html.twig',[
           'changePasswordForm' => $form->createView(),
        ]);
    }

}