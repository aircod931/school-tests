<?php
namespace App\Form;
use App\Entity\QuizQuestion;
use App\Entity\QuizQuestionOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayAnswersLabels = array();
        for($i=0; $i<count($options['questions']); $i++)
            $arrayAnswersLabels[$options['questions'][$i]->getText()] = $options['questions'][$i]->getId();

        $builder
            ->add('text', ChoiceType::class, array(
                'choices'  => $arrayAnswersLabels,
                'expanded' => true,
            ));

    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'questions'=>array(),
        ));
    }
}