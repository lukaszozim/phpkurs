<?php

namespace App\Service;

class UserValidationService {

    public array $validationErrors = [];
    public array $data = [];


    public function __construct(array $data = [])
    {
        $this->data = $data;
        // $this->validationErrors = $validationErrors;
    }

    public function validateUserData(): void
    {

        // Validate the firstName
        $this->isValidFirstName($this->data['firstName']);
        // if (empty($this->data['firstName'])) {

        //     $this->validationErrors[] = 'First name is required!';
        // }

        // Validate the lastName
        $this->isValidLastName($this->data['lastName']);
        // if (empty($this->data['lastName'])) {

        //     $this->validationErrors[] = 'Last name is required';
        // }

        // Validate the email address
        $this->isValidEmail($this->data['email']);
        // if (empty($this->data['email'])) {

        //     $this->validationErrors[] = 'Email is required';
        // } elseif (!$this->isValidEmail($this->data['email'])) {

        //     $this->validationErrors[] = 'Invalid email address';
        // }

        // Validate the phoneNumber
        $this->isValidPhoneNumber($this->data['phoneNumber']);

    }

    private function isValidFirstName($firstName)
    {
        if (empty($firstName)) {

            $this->validationErrors[] = 'First name is required!';
        }
    }

    private function isValidLastName($lastName)
    {
        if (empty($lastName)) {

            $this->validationErrors[] = 'Last name is required';
        }
    }

    private function isValidEmail($email)
    {
        if (empty($email)) {

            $this->validationErrors[] = 'Email is required';

        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->validationErrors[] = 'Invalid email address';
        }
        // Use a simple regex to check if the email format is valid
        // return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidPhoneNumber($phoneNumber)
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