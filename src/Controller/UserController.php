<?php

namespace App\Controller;

use App\Exceptions\AddressRemovalException;
use App\Exceptions\EntityRetrievalException;
use Exception;
use App\Vars\Roles;
use App\DTO\UserDTO;
use App\Service\UserServices;
use App\Exceptions\UserValidationException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Exceptions\AddressValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class UserController extends AbstractController
{

    public function __construct(private readonly UserServices $userServices, private readonly PaginatorInterface $paginator)
    {
        
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users', name: 'app_users', methods:["GET"])]
    public function index(Request $request): JsonResponse
    {
        try {
            $users= $this->userServices->getAllUsers();

            $paginatedUsers = $this->paginator->paginate(
                $users,
                $request->query->getInt("page", 1),
                3
            );

            return $this->json($paginatedUsers, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);

        }catch (Exception $e) {
            if($e instanceof EntityRetrievalException) {
                return new JsonResponse(['message' => ['message' => $e->getMessage(), 'code' => $e->getCode()]]);
            }
        }
            return new JsonResponse('Jakas response');
    }

    /**
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users/{id}', name: 'app_user', methods: ["GET"])]
    public function getUserById(string $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userServices->getUserById($id);
            return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);

        } catch (Exception $e) {
            if($e instanceof EntityRetrievalException) {
                return new JsonResponse(['message' => ['message' => $e->getMessage(), 'code' => $e->getCode()]]);
            }
        }

        return new JsonResponse('Unforeseen error occurred.', 400);

    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/users ', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer): JsonResponse
    {
//tutaj trycatch?/
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

            return $this->json('Unforeseen Error Occurred!' . $e);
        }

        return $this->json($user, 200, [], ['groups' => Roles::setRoleOnRequest($request)]);
    }

    /**
     * @param $id
     * @param $addressType
     * @return JsonResponse
     */
    #[Route('/users/{id}/addresses/{addressType}', name: 'delete_address', methods: ['DELETE'])]
    public function deleteAddress($id, $addressType): JsonResponse
    {
        try {
            $this->userServices->deleteAddress($id, $addressType);

        } catch (Exception $e) {
            if ($e instanceof AddressValidationException) {
                return $this->json($e->getMessage());
            }
            if ($e instanceof AddressRemovalException) {
                return $this->json($e->getMessage());
            }
        }

        return new JsonResponse(['message'=>'Address type '.strtoupper($addressType).' successfully deleted!']);
    }

}
