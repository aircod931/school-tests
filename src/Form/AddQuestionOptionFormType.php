<?php


namespace App\Form;


use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AddQuestionOptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quizQuestion_id', EntityType::class, [
                'class' => QuizQuestion::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('q')
                        ->orderBy('q.id', 'ASC');
                },
                'choice_label' => 'text',
            ])
            ->add('text')
        ->add('correct', CheckboxType::class, [
        'label'    => 'Czy to prawidłowa odpowiedź?',
        'required' => false,
    ]);
    }
}