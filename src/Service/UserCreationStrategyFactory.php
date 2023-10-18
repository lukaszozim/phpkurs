<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserCreationInterface;
use App\Interfaces\UserCreatorStrategyInterface;

class UserCreationStrategyFactory {


    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserDTO $userDto, readonly private UserCreationInterface $userCreator)
    {
    }


    public function createUserStrategy () : UserCreatorStrategyInterface
    {

        $strategy = match (true) {

            $this->isAdmin($this->userDto)          => new AdminUserStrategy(),
            $this->userDto->phoneNumber === 666666  => new VipUserStrategy(),
            default                                 => new SimpleUserStrategy()
        };



        return $strategy;

    }

    


    private function isAdmin($userDto): bool
    {

        $domain = explode('@', $userDto->email);

        if ($domain[1] === 'gmail.com') {

            return true;
        } else {

            return false;
        }
    }

}