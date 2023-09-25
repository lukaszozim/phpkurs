<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;

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
        return $this->userRepository->find($id) ?? null;
    }

    public function createUser(UserDTO $userDto) {

        //validacja
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);

        $user->setRole("ADMIN");
        
        return $this->userRepository->save($user);

    }
    
}
