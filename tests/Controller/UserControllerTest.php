<?php
//
//namespace App\tests\Controller;
//
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Symfony\Component\DependencyInjection\Loader\Configurator\request;
//
//class UserControllerTest extends WebTestCase
//{
//    public function testGetAllUsersEndpoint()
//    {
//        $client = static::createClient();
//        $client->request('GET', '/users');
//        // dd($client);
//        $response = $client->getResponse();
//        $response->viewData();//??;
//        $content = $response->getContent();
//        $statusCode = $response->getStatusCode();
//        $actual = json_decode($response->getContent(), true);
//        $this->assertEquals(Response::HTTP_OK, $statusCode);
//        $this->assertJson($content);
//        $this->assertOK($content);//??
//
//        $data = json_decode($content, true);
//        $this->assertIsArray($data);
//        // $this->assertEquals(200, $response->getStatusCode());
//        // $this->assertSame($result, $actual);
//    }
//
//}