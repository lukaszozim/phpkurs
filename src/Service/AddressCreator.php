<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\AddressDTO;
use App\Entity\Address;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Interfaces\UserCreationInterface;
use App\Interfaces\UserCreatorStrategyInterface;

class AddressCreator 
{


    public function __construct(private readonly AddressRepository $addressRepository)
    {

    }


    public function create(AddressDto $address, User $user ): Address
    {

        $address1 = new Address();
        $address1
            ->setCity($address->City)
            ->setStreet($address->Street)
            ->setZipCode($address->ZipCode)
            ->setType($address->type)
            ->setUser($user);
            
        $this->addressRepository->save($address1);

        return $address1;
    }


}
