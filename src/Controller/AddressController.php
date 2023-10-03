<?php

namespace App\Controller;

use App\DTO\AddressDTO;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Service\AddressCreator;
use App\Service\UserServices;
use Doctrine\DBAL\Types\VarDateTimeImmutableType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{

    public function __construct(private readonly AddressRepository $addressRepository)
    {
        
    }
    #[Route('/address', name: 'app_address')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AddressController.php',
        ]);
    }

    #[Route('/addresses ', name: 'create_address', methods: ['POST'])]
    public function createAddress(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserServices $userServices): JsonResponse
    {

        $addressData = $serializer->deserialize($request->getContent(), AddressDTO::class, "json"); //do context kolejne paraemtyr. hide, etc.;

        $errors = $validator->validate($addressData); //zwraca tablice elemetów errors

        if (count($errors) > 0) {

            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, 400);
        }
        $user  = $userServices->getUserById("018af558-33bf-7f6b-ba06-edacfa5ea2b1");
        $addressCreator = new AddressCreator($this->addressRepository);
        $address = $addressCreator->create($addressData, $user);
 // nie wiem jak to mogloby byc najlepiej przekazywane czy w body request czy tak jak tu w headers? 
// var_dump($user);
        // $address = new Address();
        // $address->setCity($addressData->City);
        // $address->setStreet($addressData->Street);
        // $address->setZipCode($addressData->ZipCode);
        // $address->setType($addressData->type);
        // $address->setUser($user);
        // $this->addressRepository->save($address);


        return $userServices->getRoleBasedSerializedData($request, $address);
        // return new JsonResponse('Dziala!');
    }

}
