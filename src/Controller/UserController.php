<?php

namespace App\Controller;

use Exception;
use Normalizer;
use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\AddressDTO;
use App\Service\UserServices;
use App\Service\AddressCreator;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


class UserController extends AbstractController
{

    public function __construct(private readonly UserServices $userServices)
    {
        
    }

    #[Route('/users', name: 'app_users', methods:["GET"])]
    public function index(Request $request)
    {
        $users= $this->userServices->getAllUsers();

        // foreach ($users as $user) {
        // var_dump($user->getId());//zeby wyciagnac uuid userów do testow. 
        // }

        return $this->userServices->getRoleBasedSerializedData($request, $users);
    }


    #[Route('/user/{id}', name: 'app_user')]
    public function getUserById($id, Request $request): JsonResponse
    {
        $user = $this->userServices->getUserById($id);

        return $this->userServices->getRoleBasedSerializedData($request, $user);
    }


    #[Route('/user/{id}/addresses', name: 'app_user_address')]
    public function getUserByIdWithAddress($id, Request $request): JsonResponse
    {
        $user = $this->userServices->getUserById($id);
        $addresses = $user->getAddresses();

        $userData = [
            'user' => $user,
            'addresses' => $addresses,
        ];

        return $this->userServices->getRoleBasedSerializedData($request, $userData);
    }

    #[Route('/users ', name: 'create_user', methods:['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator) : JsonResponse {
        
        $userData = $serializer->deserialize($request->getContent(), UserDTO::class, "json"); //do context kolejne paraemtyr. hide, etc.;

        $errors = $validator->validate($userData); //zwraca tablice elemetów errors

        if (count($errors) > 0) {

            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, 400);
        } 

        $user = $this->userServices->createUser($userData);

        return $this->userServices->getRoleBasedSerializedData($request, $user);

    }


    #[Route('/usersandaddresses ', name: 'create_user_address', methods: ['POST'])]
    public function createUserWithAddress(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, AddressCreator $addressCreator): JsonResponse
    {

        $data = json_decode($request->getContent(), true); // zeby moz wyciaganc z requesta oddzielnie user i oddzielnie address

        try {
            //wczesniej jak nie zrobilem jsonencode uzywlem $serializer->denormalize
            $userData = $serializer->deserialize(json_encode($data['user']), UserDTO::class, "json"); 
            $addressData = $serializer->deserialize(json_encode($data['address']), AddressDTO::class, "json");

        } catch (NotEncodableValueException $e) {

            return new JsonResponse(['type' => 'Invalida JSON data', 'error' => $e]);

        } catch (\Exception $e) {

            return new JsonResponse(['error' => 'An unexpected error occurred'], 500);

        }

        $errors['userData'] = $validator->validate($userData); //zwraca tablice elemetów errors
        $errors['addressData'] = $validator->validate($addressData);


        if (count($errors['userData']) > 0 || count($errors['addressData']) > 0) {

            $errorsString = (string) ($errors['userData'] . $errors['addressData']);

            return new JsonResponse($errorsString, 400);
        }

        
        $user = $this->userServices->createUserWithAddress($userData, $addressData, $addressCreator);

        return $this->userServices->getRoleBasedSerializedData($request, $user);
    }

}
