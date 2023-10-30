<?php

namespace App\Controller;

use Exception;
use Normalizer;
use App\Vars\Roles;
use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\AddressDTO;
use App\Entity\Address;
use App\Service\UserServices;
use App\Service\AddressCreator;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\UserValidationException;
use Symfony\Component\HttpFoundation\Request;
use App\Exceptions\AddressValidationException;
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
    public function index(Request $request) : JsonResponse
    {
        $users= $this->userServices->getAllUsers();

        return $this->json($users, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }


    #[Route('/users/{id}', name: 'app_user', methods: ["GET"])]
    public function getUserById($id, Request $request): JsonResponse
    {
        $user = $this->userServices->getUserById($id);

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }


    #[Route('/users ', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer): JsonResponse
    {

        $userData = $serializer->deserialize($request->getContent(), UserDTO::class, "json");

        $user = $this->userServices->createUser($userData);

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }


    #[Route('/users/{id} ', name: 'update_user', methods: ['PUT'])]
    public function updateUser($id, Request $request, SerializerInterface $serializer): JsonResponse
    {

        try {
            $updatedUserDTO = $serializer->deserialize($request->getContent(), UserDTO::class, "json");
            $user = $this->userServices->updateUser($updatedUserDTO, $id);

        } catch(Exception $e) {

            if($e instanceof UserValidationException) {
                return $this->json($e->getMessage());
            } 
            if ($e instanceof AddressValidationException) {
                return $this->json($e->getMessage());
            }
//tutaj mozna dodac kolejne user eception not found;
            return $this->json('Unforseen Error Occurred!'.$e);
        }

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }


    #[Route('/users/{id} ', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser($id, Request $request): JsonResponse
    {
        try {
        $user = $this->userServices->deleteUser($id);
        } catch (Exception $e) {

            if ($e instanceof UserValidationException) {
                return $this->json($e->getMessage());
            }
            if ($e instanceof AddressValidationException) {
                return $this->json($e->getMessage());
            }
            //tutaj mozna dodac kolejne user eception not found;
            return $this->json('Unforseen Error Occurred!' . $e);
        }

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }

    #[Route('/users/{id}/addresses/{addresType}', name: 'delete_address', methods: ['DELETE'])]
    public function deleteAddress($id, $addresType): JsonResponse
    {
        $this->userServices->deleteAddress($id, $addresType);

        return $this->json($addresType, 200, [], ['groups' => 'ADM']);
    }

}
