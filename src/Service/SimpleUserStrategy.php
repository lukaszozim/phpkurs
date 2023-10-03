<?php 

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class SimpleUserStrategy implements UserCreatorStrategyInterface 
{

    public function create(UserDTO $userDto) : User
    {

        $user = new User($userDto);
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);
        $user->setPassword((new PasswordHasher($userDto->password))->hashPassword());
        $user->setRole("SIMPLE_USER");

 
        return $user;
    }


}