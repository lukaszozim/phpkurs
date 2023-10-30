<?php

namespace App\DTO;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;


class AddressDTO
{

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $City = '';

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $type = '';

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $Street = '';

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?string $ZipCode = '';

    // #[Assert\NotNull]
    // #[NotBlank]
    public ?User $user;



}
