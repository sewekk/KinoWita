<?php

namespace App\Services;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function register(User $user, string $plainPassword): void
    {
        if ($this->userRepository->findOneBy(['email' => $user->getEmail()])) {
            throw new UserAlreadyExistsException('Konto z tym adresem e-mail już istnieje.');
        }

        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();
    }
}