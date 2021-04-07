<?php


namespace App\Form;


use App\Entity\Group;
use App\Entity\Quiz;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddTestToGroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quizQuestion', EntityType::class, [
                'class' => Quiz::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('q')
                        ->orderBy('q.id', 'ASC');
                },
                'choice_label' => 'title',
            ])
            ->add('groupName', EntityType::class, [
                'class' => Group::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.id', 'ASC');
                },
                'choice_label' => 'name',
            ]);
    }

}