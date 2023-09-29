<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Repository\UserRepository;
use App\Interfaces\UserCreatorStrategyInterface;

class UserCreator   {


    public $userCreatorStrategy;

    public function __construct(UserCreatorStrategyInterface $strategy, private readonly UserRepository $userRepository) {

        $this->userCreatorStrategy = $strategy;
    }


    public function create(UserDTO $userDto) {
        
        echo "I am creqting user";
        $this->userCreatorStrategy->create($userDto, $this->userRepository);

    }

    // zamiast tego wrzucilem w konstruktor
    // public function setStrategy(UserCreatorStrategyInterface $strategy) {
        
    //     $this->userCreatorStrategy = $strategy;
    //     var_dump($strategy);

    // }


}