<?php
declare(strict_types=1);

namespace App\Service;

use App\Exceptions\AddressRemovalException;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Exceptions\EntityRetrievalException;
use App\Repository\UserRepository;
use App\Interfaces\UserCreationInterface;
use App\Exceptions\UserValidationException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Serializer;
use App\Vars\Roles;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;


class UserServices 
{

    /**
     * @param UserRepository $userRepository
     * @param UserCreationInterface $userCreator
     * @param AddressService $addressService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        readonly private UserRepository $userRepository, 
        readonly private UserCreationInterface $userCreator, 
        readonly private AddressService $addressService,
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
     * @return array
     * @throws EntityRetrievalException
     */
    public function getAllUsers(): array
    {
        $users = $this->userRepository->findAll();

        if(count($users) === 0) throw new EntityRetrievalException('There are no users in the database.', 404);

        return $users;
    }

    /**
     * @param string $id
     * @return User|null
     * @throws EntityRetrievalException
     */
    public function getUserById (string $id): ?User
    {
        return $this->findUserById($id);

    }

    /**
     * @param string $id
     * @return User|null
     * @throws EntityRetrievalException
     */
    private function findUserById(string $id): ?User
    {
        $this->isValidUuid($id);
        $user = $this->userRepository->find($id) ?? null;
        if ($user === null ) throw new EntityRetrievalException('User could not be found', 400);
        return $user;

    }

    /**
     * @param string $uuid
     * @return bool
     * @throws EntityRetrievalException
     */
    public function isValidUuid(string $uuid): bool
    {
        try {
            Uuid::fromString($uuid);
        } catch (InvalidUuidStringException $e) {
            throw new EntityRetrievalException('The provided user id is incorrect and will not be searched. '.$e->getMessage(), 400);
        }
        return true;

    }

    /**
     * @param UserDTO $userDto
     * @return User|array
     */
    public function createUser(UserDto $userDto) : User|array
    {
        
        if($this->validateData($userDto)) 
        {
            return $this->validateData($userDto);
        } 

        $user = new UserCreationStrategyFactory($userDto, $this->userCreator);
        $this->userCreator->setStrategy($user->createUserStrategy());

        return $this->userCreator->create($userDto);

    }

    /**
     * @param UserDTO $userDto
     * @param User $user
     * @return void
     */
    private function setAllowedUpdateFields(UserDTO $userDto, User $user) : void
    {
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setRole($this->checkRole($user));

    }

    /**
     * @param UserDTO $userDto
     * @param string $id
     * @return User|null
     * @throws EntityRetrievalException
     * @throws UserValidationException
     */
    public function updateUser(UserDTO $userDto, string $id) : ?User
    {
        if($this->validateData($userDto))  throw new UserValidationException();

        $user = $this->findUserById($id);
        if (!$user) return null; //exception

        $this->setAllowedUpdateFields($userDto, $user);

        //save and stop if there are no addresses in the request;
        if(!$userDto->address) {
            return $this->userRepository->save($user);
        }

        $this->addressService->processNewAddresses($user, $userDto);
        $this->userRepository->save($user);

        return $user;

        }

    /**
     * @param $id
     * @return User|UserValidationException
     * @throws EntityRetrievalException
     * @throws UserValidationException
     */
    public function deleteUser($id) : User|UserValidationException
    {
        $user = $this->findUserById($id);
        if(!$user) {
            throw new UserValidationException('User not found', 404);
        }

        return $this->userRepository->delete($user);
    }

    /**
     * @param $id
     * @param string $addressType
     * @return bool
     * @throws AddressRemovalException
     */
    public function deleteAddress($id, string $addressType): bool
    {
        $user = $this->userRepository->find($id) ?? null;
        $validatedAddress = $this->addressService->validateAddressType($addressType, $user->getAddresses());

        if($validatedAddress  === null) {
            throw new AddressRemovalException();
        }

        return $this->userRepository->deleteAddress($validatedAddress);
    }

    /**
     * @param $user
     * @return string
     */
    public function checkRole($user): string
    {
        return match (true)
        {   
            Roles::analyzeEmail($user) && Roles::analyzePhoneNumber($user) => 'ADM',
            Roles::analyzeEmail($user) || Roles::analyzePhoneNumber($user) => 'VIP',
            default                                                        => 'SIMPLE_USER'
        };

    }

    /**
     * @param $data
     * @return array|null
     */
    private function validateData($data): array | null
    {
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {

            $errorsArray = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $message = $error->getMessage();
                $errorsArray[] = "$propertyPath: $message";
            }

            return $errorsArray;
        } 

        return null;
    }

    /**
     * @param $request
     * @param $data
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getRoleBasedSerializedData($request, $data): JsonResponse
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $data = match(true) {
            $request->headers->get('auth') === 'vip'    => $serializer->normalize($data, null, ['groups' => 'vip']),
            $request->headers->get('auth') === 'adm'    => $serializer->normalize($data, null, ['groups' => 'adm']),
            default                                     => $serializer->normalize($data, null, ['groups' => 'read'])
        };

        return new JsonResponse($data);
    }
}
