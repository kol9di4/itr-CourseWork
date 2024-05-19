<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\ItemCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Validator\Constraints\File;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
//            ->add('dateAdd', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('image', EntityType::class, [
//                'class' => Image::class,
//                'choice_label' => 'id',
//            ])
            ->add('image', FileType::class, [
                'label' => 'Image file',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/bmp',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid jpeg/png/gif/svg/bmp',
                    ])
                ],
            ])
            ->add('customItemAttributes', SymfonyCollectionType::class, [
                'entry_type' => CustomAttributeType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemCollection::class,
        ]);
    }
}
