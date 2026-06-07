<?php

namespace App\Form\Admin;

use App\Entity\Cinema;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Imię',
                'constraints' => [new NotBlank(), new Length(max: 100)],
                'attr' => ['class' => 'form-input'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nazwisko',
                'constraints' => [new NotBlank(), new Length(max: 100)],
                'attr' => ['class' => 'form-input'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adres e-mail',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-input'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $isEdit ? 'Nowe hasło (zostaw puste, by nie zmieniać)' : 'Hasło',
                'mapped' => false,
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [new NotBlank(), new Length(min: 6)],
                'attr' => ['class' => 'form-input'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rola',
                'choices' => [
                    'Klient' => 'ROLE_USER',
                    'Pracownik' => 'ROLE_STAFF',
                    'Administrator' => 'ROLE_ADMIN',
                ],
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-input',
                    'data-role-cinema-target' => 'roleSelect',
                    'data-action' => 'change->role-cinema#toggle',
                ],
            ])
            ->add('assignedCinema', EntityType::class, [
                'class' => Cinema::class,
                'label' => 'Przypisana placówka',
                'choice_label' => fn(Cinema $c) => $c->getName() . ' — ' . $c->getCity(),
                'query_builder' => fn(EntityRepository $er): QueryBuilder => $er->createQueryBuilder('c')->orderBy('c.city', 'ASC'),
                'required' => false,
                'placeholder' => '— brak —',
                'attr' => ['class' => 'form-input'],
                'row_attr' => ['data-role-cinema-target' => 'cinemaWrapper'],
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            $role = $form->get('roles')->getData();
            $cinema = $form->get('assignedCinema')->getData();

            if ($role === 'ROLE_STAFF' && $cinema === null) {
                $form->get('assignedCinema')->addError(
                    new FormError('Pracownik musi mieć przypisaną placówkę.')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
