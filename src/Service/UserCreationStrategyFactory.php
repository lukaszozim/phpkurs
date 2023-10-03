<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserCreationInterface;

class UserCreationStrategyFactory {


    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserDTO $userDto, readonly private UserCreationInterface $userCreator)
    {
    }


    public function createUserStrategy () 
    {

        if($this->isAdmin($this->userDto)) {

            $this->userCreator->setStrategy(new AdminUserStrategy());

            return $this->userCreator->getStrategy();

        } elseif ($this->userDto->phoneNumber == 666666) {

            $this->userCreator->setStrategy(new VipUserStrategy());

            return $this->userCreator->getStrategy();

        } else {

            $this->userCreator->setStrategy(new SimpleUserStrategy());

            return $this->userCreator->getStrategy();
        }

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