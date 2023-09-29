<?php 

namespace App\Service;

class PasswordHasher {

    private string $password;

    public function __construct($password)
    {   
        $this->password = $password;

    }

    public function hashPassword(): string
    {

        return password_hash($this->password, PASSWORD_DEFAULT);
    }

}


