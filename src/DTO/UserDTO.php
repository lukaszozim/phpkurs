<?php

namespace App\DTO;

class UserDTO {

    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;
    public null|int $phoneNumber;


// poniżej Homework do funkcji populate;
    // public function populate(array $params) {

    //     $objectVars = array_keys(get_object_vars($this));

    //     foreach ($objectVars as $objectParam) {

    //         foreach ($params as $param => $value) {

    //             if ($objectParam == $param) {

    //                 $this->{$objectParam} = $value;

    //             }

    //         }

    //     }

    //     return $this;

    // }

    // poniżej Homework do funkcji populate;
    // public function populate(array $params)
    // {

    //     $objectVars = array_keys(get_object_vars($this));

    //     $i = 0;

    //         foreach ($params as $param => $value) {

    //             if ($objectVars[$i] == $param && $i < count($objectVars)) {

    //                 $this->{$objectVars[$i]} = $value;

    //                 $i++;

    //             }

                

    //     }

    //     return $this;
    // }

    // public function populate(array $params)
    // {
    //     foreach (get_object_vars($this) as $key => $objectParam) {

    //         isset($params[$key]) && $this->{$key} = $params[$key];

    //     }

    //     return $this;
    // }

}