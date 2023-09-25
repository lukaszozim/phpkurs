<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Interfaces\UserInterface;
use App\Repository\UserRepository;
use App\Service\UserServices;
use App\TRASH_delete\Director;
use App\TRASH_delete\SummerHouse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Expr\Cast\Object_;
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

    #[Route('/users', name: 'app_users', methods:['GET'])]
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

        return $this->json([
            $user
        ]);
    }

    // #[Route('/users', name: 'create_user', methods: ['POST'])]
    // public function createUser(UserServices $userServices, Request $request) : Response 
    // {
    //     // file_put_contents("log.php", print_r($request->getContent(), true));
    //     // throw new Exception('ERROR!!!!');
    //     // $params = $request->request->all();

    //     $params = (array)json_decode($request->getContent());
    //     file_put_contents("log.php", print_r($params, true)); //tu juz bedzie tablica; 
    //     //serialize data
    //     $data = $userServices->serializeData($request);

    //     //validate data
    //     if ($userServices->validate($data)) {
        
    //         //add data to the database
    //         $userServices->addToDataBase($data);
    //         return new Response('User saved in the DB! ');

    //     } else {
    //         //return in case validation is not successful
    //         return new Response('User NOT SAVED IN THE DB!!!');

    //     };

    // }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(UserServices $userServices, Request $request): Response
    {

        $userDTO = $userServices->createCurrentDTOUser($request);

        try {

            $userServices->createUser($userDTO);

        } catch (Exception $e) {

                // echo "Caught exception: " . $e->getMessage(), "/n";
                return new Response($e->getMessage());
        }

        return new Response("New User has been added!");

    }

    #[Route('/delete-user/{id}', name: 'delete_user')]
    public function deleteUser(UserServices $userServices, User $user) : Response 
    {
        if(!$user) {

            return new Response ('User not found!');
        } else {

        $userServices->delete($user);

        return new Response ('User Deleted');
        }
    }

    #[Route('/testy', name: 'testy')]
    public function test()
    {

    }


}
