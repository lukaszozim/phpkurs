<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Service\UserValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserServices 
{
    /**
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserValidationService $userValidationService
     */
    private $entityManager;
    private $userValidationService;
    
    public function __construct(readonly private UserRepository $userRepository, EntityManagerInterface $entityManager, UserValidationService $userValidationService)
    {
        // $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userValidationService = $userValidationService;
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


    public function addToDataBase(array $data): void
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phoneNumber']);

        if ($this->extractRoleFromPhoneNumber($data['phoneNumber']) == 666) {
            $user->setRole('ADMIN');
        } else {
            $user->setRole('USER');
        }

        // Persist the user entity to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }


    public function validate(array $data) : bool
    {
        $newUserValidation = new UserValidationService($data);

        $newUserValidation->validateUserData($data);
        $validationErrors = $newUserValidation->validationErrors;

        if (!empty($validationErrors)) {

            foreach ($validationErrors as $error) {
                echo $error;
            }

            return false;

        } else {

            return true;

        }

    }

    private function extractRoleFromPhoneNumber(int $phoneNumber) : string
    {

        $string = (string)$phoneNumber;
        $result = (int)substr($string, 0, 3);
        
        return $result;
    }


    public function serializeData($request) : array
    {

        $data = [
            'firstName'     =>  $request->get('firstName'),
            'lastName'      =>  $request->get('lastName'),
            'email'         =>  $request->get('email'),
            'phoneNumber'   =>  $request->get('phoneNumber'),
        ];

        // $encoders = [new XmlEncoder(), new JsonEncoder()];
        // $normalizers = [new ObjectNormalizer()];
        // $serializer = new Serializer($normalizers, $encoders);

        // $jsonContent = $serializer->serialize($data, 'json');

        return $data;

    }
    
}
