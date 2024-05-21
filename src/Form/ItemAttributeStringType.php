<?php

namespace App\Form;

use App\Entity\CustomItemAttribute;
use App\Entity\Item;
use App\Entity\ItemAttributeStringField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemAttributeStringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $customItemAttributeName = $event->getData()->getCustomItemAttribute()->getName();
            $form = $event->getForm();
            $form
                ->add('value', TextType::class, [
                    'label' => $customItemAttributeName,
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemAttributeStringField::class,
        ]);
    }
}
