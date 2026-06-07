<?php

namespace App\DataFixtures;

use App\Entity\Cinema;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $cinemasData = [
            'Poznań' => [
                'name' => 'KinoWita Poznań',
                'address' => 'ul. Półwiejska 42, 61-888 Poznań',
                'hours' => '09:00 - 23:00',
            ],
            'Piła' => [
                'name' => 'KinoWita Piła',
                'address' => 'ul. Śródmiejska 10, 64-920 Piła',
                'hours' => '10:00 - 22:00',
            ],
            'Warszawa' => [
                'name' => 'KinoWita Warszawa',
                'address' => 'ul. Marszałkowska 100, 00-001 Warszawa',
                'hours' => '08:30 - 00:00',
            ],
        ];

        $createdCinemas = [];

        // 2. Loop and Persist Cinemas
        foreach ($cinemasData as $city => $data) {
            $cinema = new Cinema();
            $cinema->setName($data['name']);
            $cinema->setCity($city);
            $cinema->setAddress($data['address']);
            $cinema->setOpeningHours($data['hours']);
            
            $manager->persist($cinema);
            $createdCinemas[$city] = $cinema; 
        }

        // 3. Define Users Data
        $usersData = [
            [
                'email' => 'admin@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Jan',
                'lastName' => 'Admin',
                'roles' => ['ROLE_ADMIN'],
                'cinema' => null
            ],
            [
                'email' => 'pracownik@wsb.pl',
                'password' => 'qwerty', 
                'firstName' => 'Anna',
                'lastName' => 'Employee',
                'roles' => ['ROLE_STAFF'],
                'cinema' => 1,
            ],
            [
                'email' => 'klient@wsb.pl',
                'password' => 'qwerty',
                'firstName' => 'Piotr',
                'lastName' => 'Customer',
                'roles' => ['ROLE_USER'],
                'cinema' => null
            ],
        ];

        foreach ($usersData as $uData) {
            $user = new User();
            $user->setEmail($uData['email']);
            $user->setFirstName($uData['firstName']);
            $user->setLastName($uData['lastName']);
            $user->setRoles($uData['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $uData['password']));

            if ($uData['cinema'] !== null && isset($createdCinemas[$uData['cinema']])) {
                $user->setAssignedCinema($createdCinemas[$uData['cinema']]);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}