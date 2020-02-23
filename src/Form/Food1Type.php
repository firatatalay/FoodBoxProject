<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Food;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class Food1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'title' ,
            ])
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('image', FileType::class,[
                'label' => 'Food Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ])

            ->add('detail', CKEditorType::class, array(
                'config' =>array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ))


            // ->add('created_at')
            // ->add('updated_at')
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
