<?php

namespace App\Tests\Controller\Rest\v1;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
// use App\Tests\Controller\Rest\v1\ApiController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * php bin/phpunit tests/Controller/Rest/v1/CalculatorTest.php
 *
 * Class CategoryControllerTest
 * @package App\Tests\Controller\Rest\v1
 */
class ArticleControllerTest extends ApiController
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->setTestName("ArticleControllerTest");
        $this->setUrl("/articles");
    }

    var $token = "Bearer ";
    var $server = array(
        'HTTP_AUTHORIZATION'=>"", // for sending JWT token
        'HTTP_CONTENT_TYPE' => 'application/json', // for sending JSON data
        // 'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded; charset=UTF-8', // for sending urlencoded data
        'HTTP_ACCEPT'       => 'application/json',
    );

    function userLogin() {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();
        $userData = array(
            "username"=> "admin",
            "password" => "adminpass"
        );
        $strPostJSONData = json_encode($userData);
        $crawler = $client->request(
            'POST',
            "localhost:8080/api/login",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $strPostJSONData
        );
        $response = $client->getResponse();
        $responseContent = $response->getContent();
        $this->token = $this->token . json_decode($responseContent, true);
    }

    /**
     * @param array $data
     * @return array
     */
    static function getEntityData(array $data = array()){
        $index = rand(0, 1000);

        /*
        $data = new Category();
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($data, 'json');
        */

        $randData = array(
            "category"=>3,
            "title"=> "test article " .  $index,
            "summary" => "this description of category " .  $index,
            "content"=>"here is the content of article",
            "publishedAt"=>date("Y-m-d H:i:s")
        );
        return array_merge($randData,$data );
    }

    public function testAPIPost()
    {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();

        // create a now object of Category
        $postData = self::getEntityData();
        $strPostJSONData = json_encode($postData);
        var_dump($strPostJSONData);
        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'POST',
            ApiController::BASE_API_URL . $this->getUrl(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $strPostJSONData
        );
        $response = $client->getResponse();
        $responseContent = $response->getContent();
        // $data = json_decode($responseContent, true);
        // $this->assertSame(array('id' => 1, 'name' => 'igorw'), $data['users'][0]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), $responseContent);
    }

    public function testAPIGet()
    {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();
        $crawler = $client->request('GET',  ApiController::BASE_API_URL . $this->getUrl());
        //var_dump($client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $lists = $client->getResponse()->getContent();
        var_dump($lists);
    }

    /**
     * modify a exiting object
     */
    public function testAPIPut()
    {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();
        $arrData = array(
            "id"=>3,
            "name"=>"The contents of 1 rec. have been modified.",
        );
        $strJSONData = json_encode(CategoryControllerTest::getEntityData($arrData));

        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'PUT',
            ApiController::BASE_API_URL .$this->getUrl()."/1",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $strJSONData
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(),$client->getResponse()->getContent());
    }
    /**
     * Retrieve a exiting object
     */
    public function testAPIRetrieve()
    {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();

        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'GET',
            ApiController::BASE_API_URL . $this->getUrl()."/1"
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(),$client->getResponse()->getContent());
    }

    /**
     * modify a exiting object
     */
    public function testAPIDelete()
    {
        /** @var KernelBrowser $client */
        $client =  $this->getTestClient();

        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'DELETE',
            ApiController::BASE_API_URL . $this->getUrl()."/1"
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode(),$client->getResponse()->getContent());
    }
}
