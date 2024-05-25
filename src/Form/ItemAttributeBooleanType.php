<?php

namespace App\Form;

use App\Entity\ItemAttributeBooleanField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemAttributeBooleanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $customItemAttributeName = $event->getData()->getCustomItemAttribute()->getName();
            $form = $event->getForm();
            $form
                ->add('value', CheckboxType::class, [
                    'label' => $customItemAttributeName,
                    'required' => false,
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemAttributeBooleanField::class,
        ]);
    }
}
