<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('itemAttributeIntegerFields',\Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                    'entry_type' => ItemAttributeIntegerType::class,
                    'by_reference' => false,
                ])
            ->add('itemAttributeStringFields',\Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'entry_type' => ItemAttributeStringType::class,
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
