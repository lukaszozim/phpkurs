<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserCreationInterface;
use App\Interfaces\UserCreatorStrategyInterface;
use App\Vars\Roles;

class UserCreationStrategyFactory {


    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserDTO $userDto, readonly private UserCreationInterface $userCreator)
    {
    }


    public function createUserStrategy () : UserCreatorStrategyInterface
    {

        return match (true) {
            Roles::analyzeEmail($this->userDto) && Roles::analyzePhoneNumber($this->userDto)    => new AdminUserStrategy(),
            Roles::analyzeEmail($this->userDto) || Roles::analyzePhoneNumber($this->userDto)    => new VipUserStrategy(),
            default                                                                             => new SimpleUserStrategy()

        };

    }


}