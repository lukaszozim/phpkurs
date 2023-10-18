<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Entity\Address;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use Symfony\Component\HttpFoundation\Request;

class AddressService
{

    const AVAILABLE_ADDRESS_TYPES = ['PRIVATE', 'BUSINESS', 'CORRESPONDANCE'];

    public function __construct(private readonly AddressRepository $addressRepository, private readonly UserRepository $userRepository)
    {

    }


    public function updateAddress($newAddress, $currentAddress) : Address
    {
        //11. update address from update addressmethod in UserServices;
        $currentAddress
            ->setCity($newAddress->City)
            ->setZipCode($newAddress->ZipCode)
            ->setStreet($newAddress->Street);

        $this->addressRepository->save($currentAddress);

        return $currentAddress;
    }

    public function addNewAddress(array $newAddresses, User $user) : User
    {
        // echo "i am adding a new address to the user..";
        // print_r($newAddresses);


            foreach($newAddresses as $newAddress){

                $address = new Address();
                $address
                    ->setUser($user)
                    ->setZipCode($newAddress->ZipCode)
                    ->setCity($newAddress->City)
                    ->setType($newAddress->type)
                    ->setStreet($newAddress->Street);
                
                $user->addAddress($address);

            }

        $this->userRepository->save($user);

        return $user;
    }
}
