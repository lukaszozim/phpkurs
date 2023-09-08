<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserServices 
{
    public function __construct(private UserRepository $userRepository) 
    {
        
    }

    public function showTest() : string 
    {
        
        $message = "This is showTest!";

        return $message;

    }

    /**
     * @return User[] Returns an array of User objects
     */

    public function getAllUsers() : array
    {
        $users = $this->userRepository->findAll();
        // print_r($users);
        return $users;
        
    }

    //nie wiedzialem jaki tu zrobic typehit co zwraca ta metoda; zostawiam na razie puste
    public function getUserById (int $id)  
    {
        $user = $this->userRepository->find($id);
        return $user;
    }
    
}
