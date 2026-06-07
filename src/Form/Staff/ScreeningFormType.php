<?php

namespace App\Form\Staff;

use App\Entity\Cinema;
use App\Entity\CinemaHall;
use App\Entity\Movie;
use App\Entity\Screening;
use App\Repository\CinemaHallRepository;
use App\Repository\MovieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreeningFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Cinema|null $cinema */
        $cinema = $options['cinema'];

        $builder
            ->add('movie', EntityType::class, [
                'label' => 'Film',
                'class' => Movie::class,
                'choice_label' => 'name',
                'placeholder' => 'Wybierz film',
                'query_builder' => fn (MovieRepository $repository) => $repository->createQueryBuilder('m')
                    ->andWhere('m.isActive = :active')
                    ->setParameter('active', true)
                    ->orderBy('m.name', 'ASC'),
            ])
            ->add('hall', EntityType::class, [
                'label' => 'Sala',
                'class' => CinemaHall::class,
                'choice_label' => 'name',
                'placeholder' => 'Wybierz salę',
                'query_builder' => function (CinemaHallRepository $repository) use ($cinema) {
                    $queryBuilder = $repository->createQueryBuilder('h')
                        ->orderBy('h.name', 'ASC');

                    if (!$cinema) {
                        return $queryBuilder->andWhere('1 = 0');
                    }

                    return $queryBuilder
                        ->andWhere('h.cinema = :cinema')
                        ->setParameter('cinema', $cinema);
                },
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'Data i godzina seansu',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Seans aktywny',
                'required' => false,
            ])
        ;

        if ($options['include_repeat']) {
            $builder->add('repeatDays', IntegerType::class, [
                'label' => 'Powtarzaj przez ile kolejnych dni?',
                'mapped' => false,
                'required' => false,
                'data' => 0,
                'attr' => [
                    'min' => 0,
                    'max' => 30,
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Screening::class,
            'cinema' => null,
            'include_repeat' => false,
        ]);

        $resolver->setAllowedTypes('cinema', ['null', Cinema::class]);
        $resolver->setAllowedTypes('include_repeat', 'bool');
    }
}