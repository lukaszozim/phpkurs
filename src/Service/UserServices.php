<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Config\SecurityConfig;
use App\Interfaces\UserCreationInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


class UserServices 
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserRepository $userRepository, readonly private UserCreationInterface $userCreator)
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

    public function createUser(UserDto $userDto) : User {

        // $userCreator = new UserCreator($this->userRepository);
        
        if ($userDto->phoneNumber == 666666) {

            $this->userCreator->setStrategy(new VipUserStrategy());
            $user = $this->userCreator->create($userDto, $this->userRepository);

        } else {

            $this->userCreator->setStrategy(new SimpleUserStrategy());
            $user = $this->userCreator->create($userDto, $this->userRepository);

        };

        return $user;
    }
    
    
}
