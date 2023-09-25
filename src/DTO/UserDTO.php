<?php

namespace App\DTO;

class UserDTO {

    public string $lastName;
    public string $firstName;
    public string $email;
    public int $phoneNumber; 


    //funckja populate do ktorej przekazuje array z elementami
    //potem pętla i sprawdzenie czy istenie taka wartosc 
    // ?????
    public function populate(array $params) 
    {
        //params to request zamieniony na array (4 klucze)
        //foreach szukammy po kluczach; 
        //
        $arrayKyes = array_keys($params);

        foreach ($params as $param) {
            $this->lastName = $param['lastName'];
        }

    }


    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}