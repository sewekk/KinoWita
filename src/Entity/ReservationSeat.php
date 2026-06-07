<?php

namespace App\Entity;

use App\Repository\ReservationSeatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationSeatRepository::class)]
#[ORM\UniqueConstraint(
    name: 'uniq_screening_seat',
    columns: ['screening_id', 'rowNumber', 'seatNumber']
)]
class ReservationSeat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Screening $screening = null;

    #[ORM\Column(name: 'rowNumber')]
    private ?int $rowNumber = null;

    #[ORM\Column(name: 'seatNumber')]
    private ?int $seatNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getScreening(): ?Screening
    {
        return $this->screening;
    }

    public function setScreening(?Screening $screening): static
    {
        $this->screening = $screening;

        return $this;
    }

    public function getRowNumber(): ?int
    {
        return $this->rowNumber;
    }

    public function setRowNumber(int $rowNumber): static
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(int $seatNumber): static
    {
        $this->seatNumber = $seatNumber;

        return $this;
    }
}