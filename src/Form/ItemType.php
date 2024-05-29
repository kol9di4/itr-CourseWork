<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
            ])
//            ->add('tag', EntityType::class, [
//                'class' => Tag::class,
//                'choice_label' => 'name',
//                'multiple' => true,
//            ])
//            ->add('tag', TextType::class, [
//                'mapped'=> false,
//                'required' => false,
//                'empty_data' => '',
//                'attr' =>    [
//                    'value' => $options['tags'],
//                    'name' => 'basic',
//                    'class' => 'container rounded-1 mb-3',
//                    'placeholder' => 'Type tag here',
//                    'data-whitelist' => $options['whitelist-tags'],
//                    'id' => 'tags',
//                ],
//                'constraints' => [
//                    new NotBlank(),
//                ]
//            ])
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
//            'tags' => '',
//            'whitelist-tags' => '',
//        ]);
//        $resolver->setAllowedTypes('tags', 'string');
//        $resolver->setAllowedTypes('whitelist-tags', 'string');
//    }
}
