<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Service\UserValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    
    //?? metoda na utowrzenie instancji DTOUser wraz z parametrami z request

    public function createCurrentDTOUser($request) {
        $params = (array)json_decode($request->getContent()); //odkodowanie danych z request w postaci json; 

        $userDTO = new UserDTO();

        $userDTO->setLastName($params['lastName']);
        $userDTO->setFirstName($params['firstName']);
        $userDTO->setEmail($params['email']);
        $userDTO->setPhoneNumber($params['phoneNumber']);

        return $userDTO;
    }

//create i tu wrzucam DTOUSer;
    public function createUser(UserDTO $dtoUser) {

        if ($this->validate($dtoUser)) {

        $creator = new UserCreator($dtoUser);
        $creator->createUser($this->entityManager);

        } else {

            throw new Exception('Error: User not added');
            
        }
    }


    public function validate(UserDTO $dtoUser) : bool
    {
        $newUserValidation = new UserValidationService($dtoUser);

        $newUserValidation->validateUserData($dtoUser);
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

    public function delete(User $user) 
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

    }
    
}
