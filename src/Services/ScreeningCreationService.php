<?php

namespace App\Services;

use App\Entity\Screening;
use Doctrine\ORM\EntityManagerInterface;

class ScreeningCreationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function create(Screening $baseScreening, int $repeatDays): void
    {
        $repeatDays = max(0, $repeatDays);

        for ($i = 0; $i <= $repeatDays; $i++) {
            $screening = $i === 0 ? $baseScreening : new Screening();

            if ($i > 0) {
                $screening
                    ->setMovie($baseScreening->getMovie())
                    ->setCinema($baseScreening->getCinema())
                    ->setHall($baseScreening->getHall())
                    ->setStartsAt($baseScreening->getStartsAt()->modify('+' . $i . ' days'))
                    ->setIsActive($baseScreening->isActive());
            }

            $this->em->persist($screening);
        }

        $this->em->flush();
    }
}