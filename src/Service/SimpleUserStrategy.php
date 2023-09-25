<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Interfaces\UserCreatorStrategyInterface;

class SimpleUserStrategy implements UserCreatorStrategyInterface {



    public function createUser($dtoUser, EntityManagerInterface $entityManager): User
    {
        $newUser = new User();
        $newUser->setFirstName($dtoUser->getFirstName());
        $newUser->setLastName($dtoUser->getLastName());
        $newUser->setEmail($dtoUser->getEmail());
        $newUser->setPhoneNumber($dtoUser->getPhoneNumber());
        $newUser->setRole("SIMPLE_User");

        // Persist the user entity to the database
        $entityManager->persist($newUser);
        $entityManager->flush();

        return $newUser;
        }



    }
