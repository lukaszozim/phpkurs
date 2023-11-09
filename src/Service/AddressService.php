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

    public function __construct(
        private readonly AddressRepository $addressRepository,
        private readonly UserRepository $userRepository,
        private readonly UserDTO $userDto)
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
                    ->setZipCode($newAddress['ZipCode'])
                    ->setCity($newAddress->City)
                    ->setType($newAddress->type)
                    ->setStreet($newAddress->Street);
                $user->addAddress($address);

            }

        $this->userRepository->save($user);

        return $user;
    }


    private function addFreshAddresses(User $user, $userDto): self
    {
        if (count($user->getAddresses() ) === 0) {
            $this->addNewAddress($userDto->address, $user);
        }
        return $this;
    }

    private function updateExistingAddresses($userDto, $user): self
    {
        foreach ($user->getAddress() as $currentAddress) {
            $this->updateMatchedAddress($userDto->address, $currentAddress);
        }

        return $this;
    }

    private function addExtraAddresses($userDto, $user): self
    {
        $addressesToAdd = [];
        foreach ($userDto->address as $newAddress) {
            $exists = false;
            foreach ($user->getAddress() as $currentAddress) {
                if ($newAddress->type === $currentAddress->getType()) {
                    $exists = true;
                }
            }

            if (!$exists) {
                $addressesToAdd[] = $newAddress;
            }
        }

        $this->addNewAddress($addressesToAdd, $user);

        return $this;
    }

    public function processNewAddresses(User $user, UserDTO $userDto): void
    {
        $this->addFreshAddresses($user, $userDto)
            ->updateExistingAddresses($userDto, $user)
            ->addExtraAddresses($userDto, $user);
    }

}
