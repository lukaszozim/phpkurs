<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserServices 
{
    /**
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    private $entityManager;
    
    public function __construct(readonly private UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function showTest() : string
    {
        
        $message = "This is showTest!";

        return $message;

    }

    /**
     * @return User[] Returns an array of User objects
     */

    /**
     * @return array<User|null>
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById (int $id): ?User
    {
        return $this->userRepository->find($id) ?? null;
    }


    private function setUserData($data) 
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phoneNumber']);

        if ($this->extractRoleFromPhoneNumber($data['phoneNumber']) === 666) {
            $user->setRole('ADMIN');
        } else {
            $user->setRole('USER');
        }

        return $user;
    }
    // public function createUser($firstName, $lastName, $email, $phoneNumber) {

    public function createUser($data)
    {
        $user = $this->setUserData($data);

        $validationErrors = $this->validateUserData($data);

        //validate;
        if (!empty($validationErrors)) {

                foreach ($validationErrors as $error) {
                    echo $error;
                }

            // return new JsonResponse(['errors' => $validationErrors], Response::HTTP_BAD_REQUEST);
            return false;

        } else {
            // Persist the user entity to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true ;
        }

        // serialize


        // $userSerialized = $this->serializeData($user);
        // var_dump($userSerialized->firstName);

        // return $user;
    }

    private function extractRoleFromPhoneNumber($phoneNumber) 
    {

        $string = (string)$phoneNumber;
        $result = (int)substr($string, 0, 3);
        
        return $result;
    }

    public function validateUserData(array $data) : array 
    {

        $validationErrors = [];

        // Validate the firstName
        if (empty($data['firstName'])) {

            $validationErrors[] = 'First name is required!';

        }

        // Validate the lastName
        if (empty($data['lastName'])) {

            $validationErrors[] = 'Last name is required';

        }

        // Validate the email address
        if (empty($data['email'])) {

            $validationErrors[] = 'Email is required';

        } elseif (!$this->isValidEmail($data['email'])) {

            $validationErrors[] = 'Invalid email address';

        }

        // Validate the phoneNumber
        if (empty($data['phoneNumber'])) {

            $validationErrors[] = 'Phone number is required!';

        } 
        if (!$this->isValidPhoneNumber($data['phoneNumber'])) 
        {
            $validationErrors[] = 'Phone number must be 6 characters long!';
        }

        return $validationErrors;
    }

    private function isValidEmail($email)
    {
        // Use a simple regex to check if the email format is valid
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidPhoneNumber($phoneNumber)
    {

        if (strlen((string)$phoneNumber) !== 6) {
            
            return false;

        } else {

            return true;

        }

    }

    public function serializeData($data) {
        
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($data, 'json');

        return $jsonContent;

    }
    
}
