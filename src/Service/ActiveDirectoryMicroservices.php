<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActiveDirectoryMicroservices
{

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