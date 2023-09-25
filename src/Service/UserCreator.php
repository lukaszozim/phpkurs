<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Service\VIPUserStrategy;
use Doctrine\ORM\EntityManagerInterface;
use App\Interfaces\UserCreatorStrategyInterface;

class UserCreator {

    public $user;


    public function __construct(UserDTO $user)
    {
        $this->user = $user;

    }

    public function createUser(EntityManagerInterface $entityManager) {

        if ($this->extractPhoneNumberType($this->user->getPhoneNumber()) == 666) {

            $newVIPUser = new VIPUserStrategy();
            $newVIPUser->createUser($this->user, $entityManager);

        } else {

            $newSimpleUser = new SimpleUserStrategy();
            $newSimpleUser->createUser($this->user, $entityManager);

        }

    }

    private function extractPhoneNumberType(int $phoneNumber): string
    {

        $string = (string)$phoneNumber;
        $result = (int)substr($string, 0, 3);

        return $result;
    }
    

}