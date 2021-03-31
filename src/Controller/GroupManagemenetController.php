<?php


namespace App\Controller;
use App\Entity\Group;
use App\Form\AddGroupFormType;
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

        return $this->render('settings/groups.twig', [
            'addGroupForm'=>$form_addGroup->createView()
        ]);
    }
}