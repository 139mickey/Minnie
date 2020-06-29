<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20-4-29
 * Time: 下午8:06
 */

namespace App\Controller\Rest\v1;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityNotFoundException;

use App\Entity\User;
use App\Form\UserType;
/**
 * Make the URI of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class UserController extends AbstractFOSRestController {

    /**
     * Lists all Objects.
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getAction()
    {

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $view = $this->view($users, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * Create User.
     * @Rest\Post("/movie")
     *
     * @param Request $request
     * @return Response
     */
    public function postObjectAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /**
             * $encrypt_pass = password_hash(trim($pass), PASSWORD_BCRYPT);
             * $user->setPass($encrypt_pass);
             */

            $em->persist($user);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Retrieves an Article resource
     *
     * @Rest\Get("/users/{userId}")
     * @param \App\Controller\Rest\v1\int|int $userId
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function getObjectAction(int $userId) //: View
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findById($userId);
        if (!$user) {
            throw new EntityNotFoundException('user with id '.$userId.' does not exist!');
        }

        // In case our GET was a success we need to return a 200 HTTP OK response with the request object
        $view = $this->view($user, Response::HTTP_OK);
        return $this->handleView($view);
    }


    /**
     * @param \App\Controller\Rest\v1\int|int $userId
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteObjectAction(int $userId) //: View
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findById($userId);

        if (!$user) {
            throw new EntityNotFoundException('user with id '.$userId.' does not exist!');
        }
        $em = $this->getDoctrine()->getManager();
        $em->delete($user);


        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }


    /**
     * Retrieves an Article resource
     *
     * @Rest\Get("/user/info")
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function getUserInfoAction() //: View
    {
        $user = new User();
        $user->setUsername("zhangbing");
        $user->setFullName("zhangbing");
        $user->setEmail("abc@abc.com");
        $view = $this->view($user, Response::HTTP_OK);
        return $this->handleView($view);
    }

}