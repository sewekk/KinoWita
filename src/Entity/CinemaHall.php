<?php

namespace App\Entity;

use App\Repository\CinemaHallRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CinemaHallRepository::class)]
class CinemaHall
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $rowsCount = null;

    #[ORM\Column]
    private ?int $seatsPerRow = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cinema $cinema = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRowsCount(): ?int
    {
        return $this->rowsCount;
    }

    public function setRowsCount(int $rowsCount): static
    {
        $this->rowsCount = $rowsCount;

        return $this;
    }

    public function getSeatsPerRow(): ?int
    {
        return $this->seatsPerRow;
    }

    public function setSeatsPerRow(int $seatsPerRow): static
    {
        $this->seatsPerRow = $seatsPerRow;

        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): static
    {
        $this->cinema = $cinema;

        return $this;
    }
}
