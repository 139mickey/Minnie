<?php

namespace App\Tests\Controller\Rest\v1;

//use  App\Controller\Rest\v1\MovieController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ApiController extends WebTestCase
{
    const BASE_API_URL = "/api/v1";
    var $url = "";
    var $testName = "ControllerTest";


    public function setTestName($name){
        $this->testName = $name;
    }

    public function getTestName(){
        return $this->testName;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getTestClient()
    {
        $client = WebTestCase::createClient(['test_case' => $this->getTestName()]);
        return $client;
    }
}
