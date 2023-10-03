<?php

namespace App\Interfaces;

use App\DTO\UserDTO;
use App\Entity\User;


interface UserCreatorStrategyInterface {

    public function create(UserDTO $userDto) : User;

}