<?php

namespace App\DTO;

class UserDTO {

    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public int $phoneNumber = 0;


// poniżej Homework do funkcji populate;
    public function populate(array $params) {

        $objectVars = array_keys(get_object_vars($this));

        foreach ($objectVars as $objectParam) {

            foreach ($params as $param => $value) {


                if ($objectParam == $param) {

                    $this->{$objectParam} = $value;

                }

            }

        }

        return $this;

    }

}