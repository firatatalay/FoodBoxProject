<?php

namespace App\Form\Admin;

use App\Entity\Admin\Ordering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userid')
            ->add('foodid')
            ->add('fooodid')
            ->add('name')
            ->add('surname')
            ->add('email')
            ->add('phone')
            ->add('ordertime')
            ->add('quantity')
            ->add('total')
            ->add('ip')
            ->add('message')
            ->add('note')
            ->add('status', ChoiceType::class,[
                'choices' => [
                    'True' => 'True',
                    'False' => 'False'],
            ])
//            ->add('created_at')
//            ->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ordering::class,
        ]);
    }
}
