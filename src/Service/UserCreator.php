<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserCreationInterface;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class UserCreator implements UserCreationInterface  {


    private UserCreatorStrategyInterface $userCreatorStrategy;

    public function __construct(private readonly UserRepository $userRepository)
    {

    }


    public function create(UserDTO $userDto) : User {
        
        $user = $this->userCreatorStrategy->create($userDto);  // $user = new VipUser()->create();
        $this->userRepository->save($user);

        return $user;

    }


    public function setStrategy(UserCreatorStrategyInterface $strategy) : void 
    {
        $this->userCreatorStrategy = $strategy;
    }

    public function getStrategy() : UserCreatorStrategyInterface 
    {
        return $this->userCreatorStrategy;
    }


}