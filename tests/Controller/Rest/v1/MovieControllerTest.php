<?php

namespace App\Tests\Controller\Rest\v1;

use Symfony\Component\HttpFoundation\Response;
// use App\Tests\Controller\Rest\v1\ApiController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MovieControllerTest extends ApiController
{


    public function testMoviesAPIGet()
    {
        /** @var KernelBrowser $client */
        $client =  $this->createClient(['test_case' => 'MovieControllerTest']);
        $crawler = $client->request('GET',  ApiController::BASE_API_URL . "/movies");
        //var_dump($client->getResponse()->getStatusCode());
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testMoviesAPIPost()
    {
        /** @var KernelBrowser $client */
        $client =  $this->createClient(['test_case' => 'MovieControllerTest']);
        $postData = array(
            "title"=>"This is a title of movie",
            "count"=>5
        );
        $strPostJSONData = json_encode($postData);
        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'POST',
            ApiController::BASE_API_URL . "/movies",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $strPostJSONData
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(),$client->getResponse()->getContent());
    }

    public function testMoviesAPIPut()
    {
        /** @var KernelBrowser $client */
        $client =  $this->createClient(['test_case' => 'MovieControllerTest']);
        $arrData = array(
            "title"=>"The contents of 5 rec. have been modified.",
            "count"=>15
        );
        $strJSONData = json_encode($arrData);

        // submits a form directly (but using the Crawler is easier!)
        // $client->request('POST', '/submit', ['name' => 'Fabien']);
        // submits a raw JSON string in the request body
        $crawler = $client->request(
            'PUT',
            ApiController::BASE_API_URL . "/movies/21",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $strJSONData
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(),$client->getResponse()->getContent());
    }

    /**
    public function testRegularUsersCannotAccessToTheBackend()
    {
    $client = static::createClient(array(), array(
    'PHP_AUTH_USER' => 'john_user',
    'PHP_AUTH_PW'   => 'kitten',
    ));

    $client->request('GET', '/en/admin/post/');

    $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testAdministratorUsersCanAccessToTheBackend()
    {
    $client = static::createClient(array(), array(
    'PHP_AUTH_USER' => 'anna_admin',
    'PHP_AUTH_PW'   => 'kitten',
    ));

    $client->request('GET', '/en/admin/post/');

    $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
    $client = static::createClient(array(), array(
    'PHP_AUTH_USER' => 'anna_admin',
    'PHP_AUTH_PW'   => 'kitten',
    ));

    $crawler = $client->request('GET', '/en/admin/post/');

    $this->assertCount(
    Post::NUM_ITEMS,
    $crawler->filter('body#admin_post_index #main tbody tr'),
    'The backend homepage displays the right number of posts.'
    );
    }
     */
}
