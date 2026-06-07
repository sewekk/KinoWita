<?php

namespace App\Form\Admin;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa filmu',
            ])
            ->add('ageCategory', TextType::class, [
                'label' => 'Kategoria wiekowa',
                'attr' => [
                    'placeholder' => 'np. +16',
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Film aktywny',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}