<?php

namespace App\Service;

use Exception;
use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\AddressDTO;
use App\Entity\Address;
use App\Service\AddressService;
use App\Repository\UserRepository;
use Symfony\Config\SecurityConfig;
use App\Repository\AddressRepository;
use App\Interfaces\UserCreationInterface;
use App\Exceptions\UserValidationException;
use Symfony\Component\Serializer\Serializer;
use App\Exceptions\AddressValidationException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserServices 
{

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        readonly private UserRepository $userRepository, 
        readonly private UserCreationInterface $userCreator, 
        readonly private AddressRepository $addressRepository,
        // readonly private AddressService $addressService,
        readonly private ValidatorInterface $validator)
    {
        
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
    public function getUserById ($id): ?User
    {
    
        return $this->findUserById($id);
    }

    /**
     * @param int $id
     * @return User|null
     */
    private function findUserById($id): ?User
    {
        $user = $this->userRepository->find($id) ?? null;

        return $user;
    }

    public function createUser(UserDto $userDto) : User|Array 
    {
        
        if($this->validateData($userDto)) 
        {   //return null; tak bylo ale wtedy wyrzucalo null w razie bledow 
            return $this->validateData($userDto);
        } 

        $user = new UserCreationStrategyFactory($userDto, $this->userCreator);
        $this->userCreator->setStrategy($user->createUserStrategy());

        $user = $this->userCreator->create($userDto, $this->userRepository);

        return $user;
    } 

    public function updateUser(UserDTO $userDto, $id) : ?User
    {

        if ($this->validateData($userDto)) {
            throw new UserValidationException();

        }

        $user = $this->findUserById($id);

        if (!$user) {
            
            return null;
        }

        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);


        //save and stop of there are no addresses in the request;
        if(!$userDto->address) {
            $this->userRepository->save($user); 

            return $user;
        }


        $currentAddresses = $user->getAddresses();

        $addressService = new AddressService($this->addressRepository, $this->userRepository, $currentAddresses, $userDto);
        $addressService->processNewAddresses($user, $userDto, $currentAddresses);

        $this->userRepository->save($user);

        return $user;

        } 
    
 
    private function validateData($data)
    {

        $errors = $this->validator->validate($data);

        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $message = $error->getMessage();
                $errorsString[] = "$propertyPath: $message";
            }

            return $errorsString;
        } 

        return null;
    }


    public function getRoleBasedSerializedData($request, $data)
    {

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $data = match(true) {
            $request->headers->get('auth') === 'vip'    => $data = $serializer->normalize($data, null, ['groups' => 'vip']),
            $request->headers->get('auth') === 'adm'    => $data = $serializer->normalize($data, null, ['groups' => 'adm']),
            default                                     => $data = $serializer->normalize($data, null, ['groups' => 'read'])
        };

        return new JsonResponse($data);

    }

}
