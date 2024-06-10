<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'constraints' => [
                    new NotBlank([]),
                    new Length(['min' => 6,'max' => 255]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10]),
                ],
                'data' => $options['link'],
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'Priority',
                'choices'=>[
                    'Lowest' => 'Lowest',
                    'Low' => 'Low',
                    'Medium' => 'Medium',
                    'High' => 'High',
                    'Highest' => 'Highest',
                ],
                'data' =>  'Medium'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'link' => null,
        ]);
    }
}
