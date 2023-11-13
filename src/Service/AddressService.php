<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Entity\Address;
use App\Enum\AddressTypes;
use App\Exceptions\AddressRemovalException;
use App\Exceptions\AddressValidationException;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\Collection;
use http\Message;
use Symfony\Component\HttpFoundation\JsonResponse;


class AddressService
{

    const AVAILABLE_ADDRESS_TYPES = ['PRIVATE', 'BUSINESS', 'CORRESPONDENCE'];

    public function __construct(
        private readonly AddressRepository $addressRepository,
        private readonly UserRepository $userRepository)
    {

    }


    private function updateMatchedAddress(array $newAddresses, Address $currentAddress): void
    {
        foreach ($newAddresses as $newAddress) {

            if (strtolower($newAddress['type']) === strtolower($currentAddress->getType())) {
                $this->updateAddress($newAddress, $currentAddress);

                break;
            }
        }
    }


    public function updateAddress($newAddress, Address $currentAddress) : Address
    {
        $currentAddress
            ->setCity($newAddress['City'])
            ->setZipCode($newAddress['ZipCode'])
            ->setStreet($newAddress['Street']);
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
                ->setCity($newAddress['City'])
                ->setType($newAddress['type'])
                ->setStreet($newAddress['Street']);
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
        foreach ($user->getAddresses() as $currentAddress) {
            $this->updateMatchedAddress($userDto->address, $currentAddress);
        }

        return $this;
    }

    private function addExtraAddresses($userDto, $user): void
    {
        $addressesToAdd = [];
        foreach ($userDto->address as $newAddress) {
            $exists = false;
            foreach ($user->getAddresses() as $currentAddress) {
                if ($newAddress['type'] === $currentAddress->getType()) {
                    $exists = true;
                }
            }

            if (!$exists) {
                $addressesToAdd[] = $newAddress;
            }
        }

        $this->addNewAddress($addressesToAdd, $user);

    }

    public function processNewAddresses(User $user, UserDTO $userDto): void
    {
        $this->addFreshAddresses($user, $userDto)
            ->updateExistingAddresses($userDto, $user)
            ->addExtraAddresses($userDto, $user);
    }

    public function validateAddressType(string $addressType, Collection $addresses): Address|null
    {

        if(!in_array(strtoupper($addressType), AddressTypes::values())) {
            throw new AddressRemovalException();
        }

        if(count($addresses) !== 0) {
            foreach ($addresses as $adr) {
                if (strtoupper($adr->getType()) === strtoupper($addressType)) {
                    return $adr;
            }
        }}

        return null;
    }



}