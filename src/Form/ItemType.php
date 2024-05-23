<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3,max: 255),
                ],
                'empty_data' => '',
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('itemAttributeIntegerFields',SymfonyCollectionType::class, [
                    'entry_type' => ItemAttributeIntegerType::class,
                    'entry_options' => ['label' => false],
                    'label' => false,
                    'by_reference' => false,
                ])
            ->add('itemAttributeStringFields',SymfonyCollectionType::class, [
                'entry_type' => ItemAttributeStringType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'by_reference' => false,
            ])
            ->add('itemAttributeTextFields',SymfonyCollectionType::class, [
                'entry_type' => ItemAttributeTextType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'by_reference' => false,
            ])
            ->add('itemAttributeBooleanFields',SymfonyCollectionType::class, [
                'entry_type' => ItemAttributeBooleanType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'by_reference' => false,
            ])
            ->add('itemAttributeDateFields',SymfonyCollectionType::class, [
                'entry_type' => ItemAttributeDateType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'by_reference' => false,
            ])
        ;
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
////            'data_class' => Item::class,
//        ]);
//    }
}