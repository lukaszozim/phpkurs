<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class AddressDTO
{

    #[Assert\NotNull]
    #[NotBlank]
    public ?string $City;

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $type;

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $Street;

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $ZipCode;

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?User $user;


}
