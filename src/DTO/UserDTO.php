<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;

class UserDTO {

    #[Assert\NotNull]
    #[NotBlank]
    #[Length(max: 9)]
    public string $firstName = '';

    #[Assert\NotNull]
    #[NotBlank]
    public string $lastName = '';

    #[Assert\NotNull]
    #[NotBlank]
    #[Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public string $email = '';

    #[Assert\NotNull]
    #[NotBlank]
    #[Type('int')]
    #[Length(max: 9)]
    public int $phoneNumber = 0;

}