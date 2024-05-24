<?php

namespace App\Form;

use App\Entity\CustomItemAttribute;
use App\Enum\CustomAttributeEnum as CustomAttributeTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type',enumType::class,[
                'class' => CustomAttributeTypeEnum::class
            ])
//            ->add('type',ChoiceType::class, [
//                'choices'  => [
//                    'Integer' => 'Integer',
//                    'String' => 'String',
//                    'Text' => 'Text',
//                    'Boolean' => 'Boolean',
//                    'Date' => 'Date',
//                ],
//            ])
//            enumType
        ->add('type', EnumType::class, [
                'class' => \App\Enum\CustomAttributeEnum::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomItemAttribute::class,
        ]);
    }
}
