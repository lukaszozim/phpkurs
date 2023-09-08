<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/users', name: 'app_users')]
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


    #[Route('/create-user ', name: 'create_user')]
    public function createUser(EntityManagerInterface $entityManager) : Response {
        
        $user = new User();
        $user->setFirstName('Bruce');
        $user->setLastName('Willis');
        $user->setEmail('bw@gmail.com');
        $user->setPhoneNumber(334444);
        $user->setRole('user');

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('User saved in the DB! ' . $user->getEmail());

    }

}
