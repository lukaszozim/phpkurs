<?php

namespace App\Service;

use App\DTO\AddressDTO;
use App\DTO\UserDTO;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

use App\Repository\UserRepository;
use Symfony\Config\SecurityConfig;
use App\Interfaces\UserCreationInterface;
use App\Repository\AddressRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class UserServices 
{

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(readonly private UserRepository $userRepository, readonly private UserCreationInterface $userCreator)
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
        $user = $this->userRepository->find($id) ?? null;
        
        return $user;
    }

    public function createUser(UserDto $userDto) : User 
    {

        $user = new UserCreationStrategyFactory($userDto, $this->userCreator);
        $strategy = $user->createUserStrategy();

        $this->userCreator->setStrategy($strategy);
        $user = $this->userCreator->create($userDto, $this->userRepository);

        return $user;
    }

    public function createUserWithAddress(UserDto $userDto, AddressDTO $addressDto, AddressCreator $addressCreator) : User
    {

        $user = $this->createUser($userDto);

        $addressCreator->create($addressDto, $user);


        return $user;
    }


    public function getRoleBasedSerializedData($request, $data)
    {

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        if ($request->headers->get('auth') === 'vip') {

            $data = $serializer->normalize($data, null, ['groups' => 'vip']);

            return new JsonResponse($data);

        } elseif ($request->headers->get('auth') === 'adm') {

            $data = $serializer->normalize($data, null, ['groups' => 'adm']);

            return new JsonResponse($data);

        } else {

            $data = $serializer->normalize($data, null, ['groups' => 'read']);

            return new JsonResponse($data);

        }
    }

}
