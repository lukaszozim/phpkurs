<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Model\Hydra;
use App\Service\HydraService;
use App\Service\UserServices;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Normalizer;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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
    public function index(UserServices $userServices, Request $request): JsonResponse
    {
        $users= $userServices->getAllUsers();

        return $userServices->getRoleBasedSerializedData($request, $users);

    }

    #[Route('/user/{id}', name: 'app_user')]
    public function getUserById($id, Request $request): JsonResponse
    {
        $user = $this->userServices->getUserById($id);

        return $this->userServices->getRoleBasedSerializedData($request, $user);
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

    #[Route('/users/{page} ', name: 'create_user_pag', methods:['GET'])]
    public function getUserWithPaginate(
        int $page, UserRepository
        $userRepository,
        HydraService $service,
        Request $request
    ): JsonResponse
    {
        $params = $request->query->all();
        $qb = $userRepository->getUsersQueryBuilder($params);
        $adapter = new QueryAdapter($qb);
        $pagerFanta = Pagerfanta::createForCurrentPageWithMaxPerPage($adapter, $page, 20);
        $hydra = $service->transformToHydra(
            $pagerFanta->getCurrentPageResults(),
            $pagerFanta->getCurrentPage(),
            $pagerFanta->hasNextPage() ?? $pagerFanta->getNextPage(),
            $pagerFanta->hasPreviousPage() ?? $pagerFanta->getPreviousPage(),
            $pagerFanta->count()
        );

        return $this->json($hydra,200);
    }

}
