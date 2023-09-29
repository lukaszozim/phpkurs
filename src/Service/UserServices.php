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

        return $this->userRepository->find($id) ?? null;
    }

    public function createUser(UserDto $userDto) : void {

        
        if ($userDto->phoneNumber == 666666) {

            $userCreator = new UserCreator(new VipUserStrategy(), $this->userRepository);
            $userCreator->create($userDto, $this->userRepository);

        };

        $userCreator = new UserCreator(new SimpleUserStrategy(), $this->userRepository);
        $userCreator->create($userDto, $this->userRepository);


    }
    
}
