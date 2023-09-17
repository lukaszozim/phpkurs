<?php

namespace App\Controller;

use App\Entity\User;
use App\Interfaces\UserInterface;
use App\Repository\UserRepository;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    // private $userServices;

    // public function __construct(UserServices $userServices)
    // {
    //     $this->userServices = $userServices;
    // } 
    //:::::  PYtanie czy tak mozna zrobic i potem odwolywac sie do tego za pomoca $this->userServices... czy jest jakas roznica? 

    #[Route('/users', name: 'app_users')]
    public function index(UserServices $userServices): JsonResponse
    {
        $users= $userServices->getAllUsers();
        var_dump($users);
        return $this->json([
            $users
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function getUserById(UserServices $userServices, int $id): JsonResponse
    {
        $user = $userServices->getUserById($id);

        return $this->json([
            $user
        ]);
    }


    #[Route('/create-user ', name: 'create_user')]
    public function createUser(UserServices $userServices, Request $request) : Response {

        $data = [
            'firstName'     =>  $request->get('firstName'), 
            'lastName'      =>  $request->get('lastName'), 
            'email'         =>  $request->get('email'), 
            'phoneNumber'   =>  $request->get('phoneNumber'),
        ];

        if ($userServices->createUser($data)) {
            return new Response('User saved in the DB! ');
        } else {
            return new Response('User NOT SAVED IN THE DB!!!');
        };

    }

    // to do input point as JSON
    // #[Route('/create-user ', name: 'create_user')]
    // public function createUser(UserServices $userServices, Request $request): Response
    // {
    //     $request = 
    //     $userData = json_decode($request->getContent(), true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return new Response('Invalid JSON', Response::HTTP_BAD_REQUEST);
    //     }

    //     //serialize

    //     //validate;

    //     $user = $userServices->createUser(
    //         $userData['firstName'],
    //         $userData['lastName'],
    //         $userData['email'],
    //         $userData['phoneNumber']
    //     );


    //     return new Response('User saved in the DB! ' . $user->getEmail());
    // }

}
