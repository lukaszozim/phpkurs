<?php

namespace App\Controller;

use App\Enum\AddressTypes;
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
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use App\Exceptions\AddressValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


class UserController extends AbstractController
{

    public function __construct(private readonly UserServices $userServices)
    {
        
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users', name: 'app_users', methods:["GET"])]
    public function index(Request $request) : JsonResponse
    {
//        print_r(AddressTypes::getAllValues());
        $users= $this->userServices->getAllUsers();

        return $this->json($users, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }


    /**
     * @param Uuid $id
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users/{id}', name: 'app_user', methods: ["GET"])]
    public function getUserById(Uuid $id, Request $request): JsonResponse
    {
        $user = $this->userServices->getUserById($id);
//        $link = ['user_link' => "https://127.0.0.1:8001/users/".$id];

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
//        return new JsonResponse($user, 200);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/users ', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer): JsonResponse
    {

        $userData = $serializer->deserialize($request->getContent(), UserDTO::class, "json");

        $user = $this->userServices->createUser($userData);

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
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

            return $this->json('Unforeseen Error Occurred!'.$e);
        }

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param $id
     * @param $addresType
     * @return JsonResponse
     */
    #[Route('/users/{id}/addresses/{addresType}', name: 'delete_address', methods: ['DELETE'])]
    public function deleteAddress($id, $addresType): JsonResponse
    {
        $this->userServices->deleteAddress($id, $addresType);

        return $this->json($addresType, 200, [], ['groups' => 'ADM']);
    }

    /**
     * @return JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    #[Route('/admins', name: 'app_admins', methods: ['GET'])]
    public function getAdmins(): JsonResponse
    {
        $client = HttpClient::create(['verify_peer' => false, 'verify_host' => false]);
        $response = $client->request('GET', 'https://127.0.0.1:8000/admins' );
        $data = $response->toArray();
        $emailPack = [];
        foreach ($data as $admin) {
            array_push($emailPack, $admin['email']);
        }

        return $this->json($emailPack);
    }



}
