<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;


class UserServices 
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserRepository $userRepository)
    {
        
    }

    /**
     * @return string
     */
    public function showTest() : string
    {
        
        $message = "This is showTest!";

        return $message;

    }

    /**
     * @return User[] Returns an array of User objects
     */

    /**
     * @return array<User|null>
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById (int $id): ?User
    {
        $this->userRepository->getId()->toBinary();
        return $this->userRepository->find($id) ?? null;
    }

    public function createUser(UserDto $userDto) {

        // //validacja
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);

        $user->setPassword($this->hashPassword($userDto->password));
        $user->setRole("ADMIN");

        echo "I am about to save the userDto";
        $this->userRepository->save($user);

        return $user;

    }

    private function hashPassword($password) : string {
        echo " I am hassing the password";
        return password_hash($password, PASSWORD_DEFAULT);
    }




    
}
