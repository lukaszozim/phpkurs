<?php

namespace App\Service;

class UserValidationService {

    public array $validationErrors = [];
    public array $data;


    public function __construct(array $data = [])
    {
        $this->data = $data;

    }

    public function validateUserData(): void
    {
        // Validate the firstName
        $this->isValidFirstName($this->data['firstName']);

        // Validate the lastName
        $this->isValidLastName($this->data['lastName']);

        // Validate the email address
        $this->isValidEmail($this->data['email']);

        // Validate the phoneNumber
        $this->isValidPhoneNumber($this->data['phoneNumber']);

    }

    private function isValidFirstName($firstName) : void
    {
        if (empty($firstName)) {

            $this->validationErrors[] = 'First name is required!';
        }
    }

    private function isValidLastName($lastName): void
    {
        if (empty($lastName)) {

            $this->validationErrors[] = 'Last name is required';
        }
    }

    private function isValidEmail($email): void
    {

        if (empty($email)) {

            $this->validationErrors[] = 'Email is required';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->validationErrors[] = 'Invalid email address';
        }

    }

    private function isValidPhoneNumber(string $phoneNumber): void
    {

        if (empty($phoneNumber)) {

            $this->validationErrors[] = 'Phone number is required!';
        }

        if (strlen($phoneNumber) !== 6) {

            $this->validationErrors[] = 'Phone number must be 6 characters long!';
        }

        if (!preg_match('/^[0-9]+$/', $phoneNumber)) {

            $this->validationErrors[] = 'Phone number must contain only numbers!';
        }

    }

}