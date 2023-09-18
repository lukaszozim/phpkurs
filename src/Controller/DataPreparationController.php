<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/data/preparation', name: 'app_data_preparation_')]
class DataPreparationController extends AbstractController
{
    #[Route('/deserialize', name: 'deserialize', methods: ["POST"])]
    public function index(SerializerInterface $serializer, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('read')
            ->toArray();

        /** @var User $userData */
        $userData = $serializer->deserialize($request->getContent(), User::class, 'json', $context);
        /**
         * OPIS powyższego kodu
         * proces deserializacji za pomocą wbudowanego mechanizmu w klasie Serializer.
         * Pierwszy parameter to json, drugi to klasa na którą dokonujemy deserializacji np klasa DTO, ostatni paramert można pominać jeśli to json.
         * kod nie robi nic po pobraniem danych z requesta, deserializacją na obiekt i na końcu odbywa się proces ponownej serializacji na jsona.
         *
         * Doinstalowanie serializera composer req symfony/serializer lub symfony serializer-pack
         *
         * Serializacja uwzględnia grupy serializacjia(adnotacje w encji User, zwracamy tylko te pola które mają grupę serializacji read.
         * Pole lastName jest ustawione natomiast nie ma odpowiendiej grupy dlatego nie jest zwracane w repsonse
         *
         */

        return $this->json([
            $userData
        ]);
    }
}
