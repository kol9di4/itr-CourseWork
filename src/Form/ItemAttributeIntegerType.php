<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\ItemAttributeIntegerField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemAttributeIntegerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $customItemAttributeName = $event->getData()->getCustomItemAttribute()->getName();
            $form = $event->getForm();
            $form
                ->add('value', IntegerType::class, [
                    'label' => $customItemAttributeName,
//                    'constraints' => [
//                        new Assert\NotBlank(),
//                        new Assert\Range(['min' => -2147483648, 'max' => 2147483647]),
//                    ]
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemAttributeIntegerField::class,
        ]);
    }
}
