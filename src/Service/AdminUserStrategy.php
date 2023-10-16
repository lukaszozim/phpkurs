<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\Address;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class AdminUserStrategy implements UserCreatorStrategyInterface
{

    public function create(UserDTO $userDto): User
    {
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPhoneNumber($userDto->phoneNumber);
        $user->setPassword((new PasswordHasher($userDto->password))->hashPassword());
        $user->setRole("ADM");

        foreach ($userDto->addresses as $addressDto){
            $address = new Address();
            $address
                ->setUser($user)
                ->setPostalCode($addressDto->postalCode)
                ->setCity($addressDto->city)
                ->setStreet($addressDto->street)
                ->setHouseNumber($addressDto->houseNumber)
                ->setApartmentNumber($addressDto->apartmentNumber);

            $user->addAddress($address);
        }
        return $user;
    }
}
