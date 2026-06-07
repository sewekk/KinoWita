<?php

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\Screening;
use App\Enum\MovieCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Screening>
 */
class ScreeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screening::class);
    }

    public function findByCinemaOrderedByDate(Cinema $cinema): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.cinema = :cinema')
            ->setParameter('cinema', $cinema)
            ->orderBy('s.startsAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveUpcomingByCinema(
        Cinema $cinema,
        ?string $search = null,
        ?string $category = null,
        ?\DateTimeImmutable $dateFrom = null,
        ?\DateTimeImmutable $dateTo = null,
    ): array {
        $query = $this->createQueryBuilder('s')
            ->join('s.movie', 'm')
            ->join('s.hall', 'h')
            ->andWhere('s.cinema = :cinema')
            ->andWhere('s.isActive = :active')
            ->andWhere('m.isActive = :active')
            ->andWhere('s.startsAt >= :now')
            ->setParameter('cinema', $cinema)
            ->setParameter('active', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.startsAt', 'ASC');

        if ($search) {
            $query
                ->andWhere('LOWER(m.name) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $movieCategory = MovieCategory::tryFrom($category);

            if ($movieCategory) {
                $query
                    ->andWhere('m.category = :category')
                    ->setParameter('category', $movieCategory);
            }
        }

        if ($dateFrom && $dateTo) {
            $query
                ->andWhere('s.startsAt BETWEEN :dateFrom AND :dateTo')
                ->setParameter('dateFrom', $dateFrom)
                ->setParameter('dateTo', $dateTo);
        }

        return $query->getQuery()->getResult();
    }
}
