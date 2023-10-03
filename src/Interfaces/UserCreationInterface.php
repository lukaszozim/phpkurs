<?php

namespace App\Interfaces;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserCreatorStrategyInterface;

interface UserCreationInterface {


    public function setStrategy(UserCreatorStrategyInterface $strategy) : void;

    public function create(UserDTO $userDto) : User;

    public function getStrategy();

}