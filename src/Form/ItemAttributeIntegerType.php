<?php

namespace App\Form;

use App\Entity\CustomItemAttribute;
use App\Entity\Item;
use App\Entity\ItemAttributeIntegerField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemAttributeIntegerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('item', EntityType::class, [
                'class' => Item::class,
                'choice_label' => 'id',
            ])
            ->add('customItemAttribute', EntityType::class, [
                'class' => CustomItemAttribute::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemAttributeIntegerField::class,
        ]);
    }
}
