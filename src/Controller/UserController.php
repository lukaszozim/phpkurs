<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Service\UserServices;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Normalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{

    public function __construct(private readonly UserServices $userServices)
    {
        
    }

    #[Route('/users', name: 'app_users', methods:["GET"])]
    public function index(UserServices $userServices): JsonResponse
    {
        $users= $userServices->getAllUsers();

        return $this->json([
            $users
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function getUserById(UserServices $userServices, int $id): JsonResponse
    {
        $user = $userServices->getUserById($id);
        print_r($user);
        return $this->json([
            $user
        ]);
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
        $serializationGroup = $this->userServices->serializationGroups;


        return $this->json($user, 200, [],['groups' => $serializationGroup]);

    }

}
