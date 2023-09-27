<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class UserDTO {

    #[NotBlank]
    public string $firstName;
    #[NotBlank]
    public string $lastName;
    #[NotBlank]
    public string $email;
    #[NotBlank]
    public int $phoneNumber;

}