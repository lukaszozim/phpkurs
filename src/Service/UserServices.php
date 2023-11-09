<?php
declare(strict_types=1);

namespace App\Service;

use Exception;
use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\AddressDTO;
use App\Entity\Address;
use App\Enum\AddressTypes;
use App\Service\AddressService;
use App\Repository\UserRepository;
use Symfony\Config\SecurityConfig;
use App\Repository\AddressRepository;
use App\Interfaces\UserCreationInterface;
use App\Exceptions\UserValidationException;
use Symfony\Component\Serializer\Serializer;
use App\Exceptions\AddressValidationException;
use App\Vars\Roles;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Uid\Uuid;

class UserServices 
{

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        readonly private UserRepository $userRepository, 
        readonly private UserCreationInterface $userCreator, 
        readonly private AddressRepository $addressRepository,
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
     * @param Uuid $id
     * @return User|null
     */
    public function getUserById (string $id): ?User
    {
    
        return $this->findUserById($id);
    }

    /**
     * @param Uuid $id
     * @return User|null
     */
    private function findUserById(string $id): ?User
    {
        return $this->userRepository->find($id) ?? null;

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

    private function setAllowedFields(UserDTO $userDto, User $user) : void
    {
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        // change role if the email has changed->user can change email and this can affect the status.. but can't change the phone number. only phone number and email will give adm status
        $user->setEmail($userDto->email);
        $user->setRole($this->checkRole($user));

    }

    public function updateUser(UserDTO $userDto, string $id) : ?User
    {
        $this->validateData($userDto) && throw new UserValidationException();

        $user = $this->findUserById($id);
        if (!$user) return null; //exception
        $this->setAllowedFields($userDto, $user);

        //save and stop if there are no addresses in the request;
        if(!$userDto->address)
        {
            return $this->userRepository->save($user);
        }
        
        $this->addressService->processNewAddresses($user, $userDto);
        $this->userRepository->save($user);

        return $user;

        } 

    public function deleteUser(Uuid $id) : User|UserValidationException
    {
        $user = $this->findUserById($id);

        if(!$user) {
            throw new UserValidationException('User not found', 404);
        }
        
        return $this->userRepository->delete($user);

    }

    public function deleteAddress(Uuid $id, string $addressType): void
    {

        $user = $this->getUserById($id);
        $enum = AddressTypes::getType($addressType);
        file_put_contents('log.php', print_r($enum, true)); // todo
        foreach ($user->getAddresses() as $address) {

            if (strtolower($address->getType()) === strtolower($addressType)) {

                $user->removeAddress($address);
            }
        }

        $this->userRepository->flushAddress($address);

    }

    public function checkRole($user) {

        return match (true)
        {   
            Roles::analyzeEmail($user) && Roles::analyzePhoneNumber($user) => 'ADM',
            Roles::analyzeEmail($user) || Roles::analyzePhoneNumber($user) => 'VIP',
            default                                                        => 'SIMPLE_USER'
        };

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


    public function getRoleBasedSerializedData($request, $data): JsonResponse
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
