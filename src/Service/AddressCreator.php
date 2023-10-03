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


    public function create(AddressDTO $addressDto, User $user ): Address
    {

        $address = new Address();
        $address->setCity($addressDto->City);
        $address->setStreet($addressDto->Street);
        $address->setZipCode($addressDto->ZipCode);
        $address->setType($addressDto->type);
        $address->setUser($user);
        $this->addressRepository->save($address);

        return $address;
    }


}
