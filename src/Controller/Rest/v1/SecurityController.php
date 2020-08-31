<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20-4-29
 * Time: 下午8:06
 */

namespace App\Controller\Rest\v1;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(Request $request)
    {
        $statusCode = Response::HTTP_OK;
        $viewData = [
            "data" => "success",
            "message" => "success",
            //"status" => "ok"
        ];
        $view = $this->view($viewData, $statusCode);
        return $this->handleView($view);
    }
}