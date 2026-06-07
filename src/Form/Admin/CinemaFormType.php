<?php

namespace App\Form\Admin;

use App\Entity\Cinema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CinemaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa kina',
            ])
            ->add('city', TextType::class, [
                'label' => 'Miasto',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adres',
            ])
            ->add('openingHours', TextType::class, [
                'label' => 'Godziny otwarcia',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cinema::class,
        ]);
    }
}