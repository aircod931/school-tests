<?php
namespace App\Form;
use App\Entity\QuizQuestion;
use App\Entity\QuizQuestionOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizQuestionOptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', ChoiceType::class, array(
                'label'=>'text',
                'choices' => array(
                    $options['questions'],
                ),
                'expanded' => true,
            ));
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\QuizQuestionOption',
            'questions'=>array(),
        ));
    }
}