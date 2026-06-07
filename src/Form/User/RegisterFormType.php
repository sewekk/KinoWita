<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Imię',
                'attr' => [
                    'class' => 'form-input',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nazwisko',
                'attr' => [
                    'class' => 'form-input',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'class' => 'form-input',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Hasło',
                    'attr' => [
                        'class' => 'form-input',
                    ],
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                ],
                'second_options' => [
                    'label' => 'Powtórz hasło',
                    'attr' => [
                        'class' => 'form-input',
                    ],
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                ],
                'invalid_message' => 'Hasła muszą być takie same.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}