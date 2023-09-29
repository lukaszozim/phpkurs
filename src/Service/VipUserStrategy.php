<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class VipUserStrategy implements UserCreatorStrategyInterface
{

    public function create(UserDTO $userDto, UserRepository $userRepository)
    {   
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);
        $user->setPassword((new PasswordHasher($userDto->password))->hashPassword());
        $user->setRole("ADMIN");

        return $userRepository->save($user);
    }
}
