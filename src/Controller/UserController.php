<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Service\UserServices;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator) :JsonResponse {

        $userData = $serializer->deserialize($request->getContent(), UserDTO::class, "json");

        $errors = $validator->validate($userData);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Bad request.'], 400);
        }

        $this->userServices->createUser($userData);

        return $this->json($userData, 200);
    }

}
