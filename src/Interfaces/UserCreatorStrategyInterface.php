<?php

namespace App\Interfaces;

use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

interface UserCreatorStrategyInterface
{
    public function createUser(UserDTO $dtoUser, EntityManagerInterface $entityManager): User;

}