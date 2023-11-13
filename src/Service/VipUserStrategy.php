<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Entity\Address;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class VipUserStrategy implements UserCreatorStrategyInterface
{

    public function create(UserDTO $userDto) : User
    {   
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);
        $user->setPassword((new PasswordHasher($userDto->password))->hashPassword());
        $user->setRole("VIP");

        if (isset($userDto->address)) {

            foreach ($userDto->address as $addressDto) {
                $address = new Address();
                $address
                    ->setUser($user)
                    ->setZipCode($addressDto['ZipCode'])
                    ->setCity($addressDto['City'])
                    ->setType($addressDto['type'])
                    ->setStreet($addressDto['Street']);

                $user->addAddress($address);
            }
        }

        return $user;
    }
}
