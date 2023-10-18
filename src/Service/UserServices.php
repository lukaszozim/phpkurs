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
    public function __construct(readonly private AddressService $addressService, readonly private UserRepository $userRepository, readonly private UserCreationInterface $userCreator, readonly private AddressRepository $addressRepository, readonly private ValidatorInterface $validator)
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
        //1. Validate data, throw exception in case the validation against UserDTO fails
        if (!$this->validateData($userDto)) {
            throw new UserValidationException();
        }
        //2. Find user and user->getAddresses()
        $user = $this->findUserById($id);
        //3. If user is not found (returns null) retrun null and finish
        if (!$user) {

            return null;
        }

        ///4. If user is found change only the values firstName and lastName
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);

        //5. Address:: if userDto->address is not found save user and finish
        if(!$userDto->address) {
            $this->userRepository->save($user); 

            return $user;
        }

        //6. Get all current addresses from the DB. Use user's entity to retrieve addresses.
        $currentAddresses = $user->getAddresses();
        //7. If number of addresses is 0, so there are no addresses in the DB, follow the procedure of adding a new address and finish. 
        if(count($currentAddresses) === 0) {
            $this->addressService->addNewAddress($userDto->address, $this->findUserById($id));

            return $user;
        }
        //8. If there are addresses in the DB loop through them and update the address go to ~175 fro 9.
        foreach ($currentAddresses as $currentAddress) {

                $this->updateAddress($userDto->address, $currentAddress);

            };
        //12. The priocedure when there's a new address in the request and new for the database. 
        // create an array of those addresses to add extra. 
        $addressesToAdd = [];
        //13. Loop throu the addresses from the request and set the flag $exists to false. 
        foreach ($userDto->address as $newAddress) 
        {
            $exists = false;
        //14. Loop through current addresses in the database. 
            foreach ($currentAddresses as $currentAddress) {
        //15. If the new address type is the same as the current then set the flag to true which will stop it from getting added to the addresssesToAdd array
                if ($newAddress->type === $currentAddress->getType()) {
                    $exists = true;
                }

            }
        //16. Checks the flag and if it is false, add the new address to the list.
            if(!$exists) {
                $addressesToAdd[] = $newAddress;
            }
        }
        //17. Use the method addNewAddress and add all the addresses from the array. the addNewAddress has the loop there.
            $this->addressService->addNewAddress($addressesToAdd, $user);

        //18. Save the user. 
            $this->userRepository->save($user);

            return $user;

        } 
    
        //9. Function update address accepts the new address as array and current address as Address entity. 
    private function updateAddress(array $newAddresses, Address $currentAddress) : void
    {   //10. Take new addresses and loop throu them, if there is type match update address with the method from address service and stop/break;
        foreach ($newAddresses as $newAddress) {

            if (strtolower($newAddress->type) === strtolower($currentAddress->getType())) {
                $this->addressService->updateAddress($newAddress, $currentAddress);

                break;
            }

        }
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
