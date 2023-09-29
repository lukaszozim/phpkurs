<?php

namespace App\Interfaces;

use App\DTO\UserDTO;
use App\Repository\UserRepository;

interface UserCreatorStrategyInterface {

    public function create(UserDTO $userDto, UserRepository $userRepository);

}