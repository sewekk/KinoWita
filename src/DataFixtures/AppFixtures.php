<?php

namespace App\DataFixtures;

use App\Entity\Cinema;
use App\Entity\CinemaHall;
use App\Entity\Movie;
use App\Entity\Reservation;
use App\Entity\ReservationSeat;
use App\Entity\Screening;
use App\Entity\User;
use App\Enum\MovieCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $cinemas = $this->createCinemas($manager);
        $users = $this->createUsers($manager, $cinemas);
        $halls = $this->createHalls($manager, $cinemas);
        $movies = $this->createMovies($manager);
        $screenings = $this->createScreenings($manager, $cinemas, $halls, $movies);

        $this->createReservations($manager, $users, $screenings);

        $manager->flush();
    }

    private function createCinemas(ObjectManager $manager): array
    {
        $cinemasData = [
            'poznan' => [
                'name' => 'KinoWita Poznań',
                'city' => 'Poznań',
                'address' => 'ul. Półwiejska 42, 61-888 Poznań',
                'hours' => '09:00 - 23:00',
            ],
            'pila' => [
                'name' => 'KinoWita Piła',
                'city' => 'Piła',
                'address' => 'ul. Śródmiejska 10, 64-920 Piła',
                'hours' => '10:00 - 22:00',
            ],
            'warszawa' => [
                'name' => 'KinoWita Warszawa',
                'city' => 'Warszawa',
                'address' => 'ul. Marszałkowska 100, 00-001 Warszawa',
                'hours' => '08:30 - 00:00',
            ],
        ];

        $cinemas = [];

        foreach ($cinemasData as $key => $data) {
            $cinema = new Cinema();
            $cinema->setName($data['name']);
            $cinema->setCity($data['city']);
            $cinema->setAddress($data['address']);
            $cinema->setOpeningHours($data['hours']);

            $manager->persist($cinema);
            $cinemas[$key] = $cinema;
        }

        return $cinemas;
    }

    private function createUsers(ObjectManager $manager, array $cinemas): array
    {
        $usersData = [
            'admin' => [
                'email' => 'admin@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Jan',
                'lastName' => 'Admin',
                'roles' => ['ROLE_ADMIN'],
                'cinema' => null,
            ],
            'staff_poznan' => [
                'email' => 'pracownik@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Anna',
                'lastName' => 'Pracownik',
                'roles' => ['ROLE_STAFF'],
                'cinema' => 'poznan',
            ],
            'staff_pila' => [
                'email' => 'pila@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Marek',
                'lastName' => 'Pracownik',
                'roles' => ['ROLE_STAFF'],
                'cinema' => 'pila',
            ],
            'client' => [
                'email' => 'klient@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Piotr',
                'lastName' => 'Klient',
                'roles' => ['ROLE_USER'],
                'cinema' => null,
            ],
        ];

        $users = [];

        foreach ($usersData as $key => $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

            if ($data['cinema'] !== null) {
                $user->setAssignedCinema($cinemas[$data['cinema']]);
            }

            $manager->persist($user);
            $users[$key] = $user;
        }

        return $users;
    }

    private function createHalls(ObjectManager $manager, array $cinemas): array
    {
        $hallsData = [
            'poznan_main' => ['cinema' => 'poznan', 'name' => 'Sala Główna', 'rows' => 6, 'seats' => 10],
            'poznan_small' => ['cinema' => 'poznan', 'name' => 'Sala Kameralna', 'rows' => 4, 'seats' => 8],
            'pila_main' => ['cinema' => 'pila', 'name' => 'Sala 1', 'rows' => 5, 'seats' => 9],
            'warszawa_main' => ['cinema' => 'warszawa', 'name' => 'Sala Premium', 'rows' => 7, 'seats' => 12],
        ];

        $halls = [];

        foreach ($hallsData as $key => $data) {
            $hall = new CinemaHall();
            $hall->setName($data['name']);
            $hall->setRowsCount($data['rows']);
            $hall->setSeatsPerRow($data['seats']);
            $hall->setCinema($cinemas[$data['cinema']]);

            $manager->persist($hall);
            $halls[$key] = $hall;
        }

        return $halls;
    }

    private function createMovies(ObjectManager $manager): array
    {
        $moviesData = [
            'interstellar' => [
                'name' => 'Interstellar',
                'age' => '+12',
                'category' => MovieCategory::SCI_FI,
            ],
            'joker' => [
                'name' => 'Joker',
                'age' => '+16',
                'category' => MovieCategory::DRAMA,
            ],
            'shrek' => [
                'name' => 'Shrek',
                'age' => '+7',
                'category' => MovieCategory::ANIMATION,
            ],
            'batman' => [
                'name' => 'Batman',
                'age' => '+13',
                'category' => MovieCategory::ACTION,
            ],
            'conjuring' => [
                'name' => 'Obecność',
                'age' => '+16',
                'category' => MovieCategory::HORROR,
            ],
        ];

        $movies = [];

        foreach ($moviesData as $key => $data) {
            $movie = new Movie();
            $movie->setName($data['name']);
            $movie->setAgeCategory($data['age']);
            $movie->setCategory($data['category']);
            $movie->setIsActive(true);

            $manager->persist($movie);
            $movies[$key] = $movie;
        }

        return $movies;
    }

    private function createScreenings(ObjectManager $manager, array $cinemas, array $halls, array $movies): array
    {
        $screeningsData = [
            [
                'movie' => 'interstellar',
                'cinema' => 'poznan',
                'hall' => 'poznan_main',
                'date' => '+1 day',
                'time' => [18, 30],
            ],
            [
                'movie' => 'joker',
                'cinema' => 'poznan',
                'hall' => 'poznan_small',
                'date' => '+1 day',
                'time' => [20, 45],
            ],
            [
                'movie' => 'shrek',
                'cinema' => 'poznan',
                'hall' => 'poznan_main',
                'date' => '+2 days',
                'time' => [15, 00],
            ],
            [
                'movie' => 'batman',
                'cinema' => 'pila',
                'hall' => 'pila_main',
                'date' => '+1 day',
                'time' => [19, 15],
            ],
            [
                'movie' => 'conjuring',
                'cinema' => 'warszawa',
                'hall' => 'warszawa_main',
                'date' => '+1 day',
                'time' => [21, 30],
            ],
        ];

        $screenings = [];

        foreach ($screeningsData as $index => $data) {
            [$hour, $minute] = $data['time'];

            $screening = new Screening();
            $screening->setMovie($movies[$data['movie']]);
            $screening->setCinema($cinemas[$data['cinema']]);
            $screening->setHall($halls[$data['hall']]);
            $screening->setStartsAt((new \DateTimeImmutable($data['date']))->setTime($hour, $minute));
            $screening->setIsActive(true);

            $manager->persist($screening);
            $screenings[$index] = $screening;
        }

        return $screenings;
    }

    private function createReservations(ObjectManager $manager, array $users, array $screenings): void
    {
        $reservation = new Reservation();
        $reservation->setUser($users['client']);
        $reservation->setScreening($screenings[0]);
        $reservation->setStatus('reserved');

        $seatOne = new ReservationSeat();
        $seatOne->setReservation($reservation);
        $seatOne->setScreening($screenings[0]);
        $seatOne->setRowNumber(1);
        $seatOne->setSeatNumber(5);

        $seatTwo = new ReservationSeat();
        $seatTwo->setReservation($reservation);
        $seatTwo->setScreening($screenings[0]);
        $seatTwo->setRowNumber(1);
        $seatTwo->setSeatNumber(6);

        $manager->persist($reservation);
        $manager->persist($seatOne);
        $manager->persist($seatTwo);
    }
}