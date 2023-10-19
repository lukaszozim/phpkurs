<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Entity\Address;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;

class AddressService
{

    const AVAILABLE_ADDRESS_TYPES = ['PRIVATE', 'BUSINESS', 'CORRESPONDANCE'];

    public function __construct(private readonly AddressRepository $addressRepository, private readonly UserRepository $userRepository, private readonly Collection $currentAddresses, private readonly UserDTO $userDto)
    {

    }


    private function updateMatchedAddress(array $newAddresses, Address $currentAddress): void
    {
        foreach ($newAddresses as $newAddress) {

            if (strtolower($newAddress->type) === strtolower($currentAddress->getType())) {
                $this->updateAddress($newAddress, $currentAddress);

                break;
            }
        }
    }


    public function updateAddress($newAddress, Address $currentAddress) : Address
    {
        $currentAddress
                ->setCity($newAddress->City)
                ->setZipCode($newAddress->ZipCode)
                ->setStreet($newAddress->Street);
        $this->addressRepository->save($currentAddress);

        return $currentAddress;
    }

    public function addNewAddress(array $newAddresses, User $user) : User
    {

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


    private function addFreshAddresses($user, $userDto, $currentAddresses): void
    {
        if (count($currentAddresses) === 0) {
            $this->addNewAddress($userDto->address, $user);
        }

    }

    private function updateExistingAddresses($userDto, $currentAddresses): void
    {
        foreach ($currentAddresses as $currentAddress) {
            $this->updateMatchedAddress($userDto->address, $currentAddress);
        }
    }

    private function addExtraAddresses($userDto, $user, $currentAddresses): void
    {
        $addressesToAdd = [];
        foreach ($userDto->address as $newAddress) {
            $exists = false;
            foreach ($currentAddresses as $currentAddress) {
                if ($newAddress->type === $currentAddress->getType()) {
                    $exists = true;
                }
            }

            if (!$exists) {
                $addressesToAdd[] = $newAddress;
            }
        }

        $this->addNewAddress($addressesToAdd, $user);
    }

    public function processNewAddresses(User $user, UserDTO $userDto, Collection $currentAddresses): void
    {
        $this->addFreshAddresses($user, $userDto, $currentAddresses);
        $this->updateExistingAddresses($userDto, $currentAddresses);
        $this->addExtraAddresses($userDto, $user, $currentAddresses);
    }

}
