<?php
/*
* This file is part of the Minnie package.
*
* (c) ZhangBing <550695@qq.com>
*
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\Controller\Rest\v1;

use App\Entity\User;
use App\Form\UserType;
use App\Event\ApiControllerEvent;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Context\Context;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Doctrine\ORM\EntityNotFoundException;

/**
 * Make the URI of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class UserController extends AbstractBaseApiController {

    private $oldPassword = null;

    public function __construct()
    {
        $this->setEntityClass(User::class);
        $this->setEntityFromTypeClass(UserType::class);
    }

    /**
     * @param View $view
     */
    public function SetSerializationContext(View $view){
        $serializationContext = $view->getContext();
        $serializationContext->addGroup('normal');
        // $view->getContext()->addGroup('normal');
        // $view->getContext()->setAttribute(AbstractNormalizer::IGNORED_ATTRIBUTES,['children','articles','password']);

        // all callback parameters are optional (you can omit the ones you don't use)
        /*
        $dateTimeToString = function ($dateTime) {
            return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';
        };

        $callBacks = [
            'publishedAt' => $dateTimeToString,
        ];

        $serializationContext->setAttribute( AbstractNormalizer::CALLBACKS, $callBacks);
        */
    }
    /**
     * Lists all Objects.
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getAction()
    {
        return $this->getEntitiesAction();
    }

    public function entityPrePersistOnAction($object){
        //$object
    }

    /**
     * register a new User.
     * @Rest\Post("/users")
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        $this->addEventListener(ApiControllerEvent::POST_PUT, function (ApiControllerEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            /** @var UserPasswordEncoderInterface $passwordEncoder */
            $passwordEncoder = $this->container->get('security.password_encoder');
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
        });
        return $this->createEntityAction($request);
    }
 
    /**
     * Modify a specified Entity object by id and content.
     * @Rest\Put("/users/{id}")
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function putAction(Request $request, int $id)
    {
        $oldPassword = null;
        $this->addEventListener(ApiControllerEvent::PRE_POST_PUT, function (ApiControllerEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            /** @var  UserController $userController */
            $userController = $event->getController();
            if($user){
                // if user don't change the password then save old password first.
                $oldPassword = $user->getPassword();
                $userController->setOldPassword($oldPassword);
            }
        });

        $this->addEventListener(ApiControllerEvent::POST_PUT, function (ApiControllerEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            /** @var  UserController $userController */
            $userController = $event->getController();

            if($user && $user->getId()>0){
                // if user do not modify old password then restore it
                if(null == $user->getPassword()){
                    $oldPassword = $userController->getOldPassword();
                    $user->setPassword($oldPassword);
                }else{
                    /** @var UserPasswordEncoderInterface $passwordEncoder */
                    $passwordEncoder = $this->container->get('security.password_encoder');
                    $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($password);
                }
            }
        });
        return $this->modifyEntity($request,$id);
    }

    /**
     * Retrieves an Category resource
     *
     * @Rest\Get("/users/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction(int $id) //: View
    {
        return $this->getEntityAction($id);
    }

    /**
     * @Rest\Delete("/users/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteAction(int $id) //: View
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
        return $this->deleteEntity($id);
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
        $statusCode = Response::HTTP_OK;
        $message ="success";
        /** @var User $user */
        $user = $this->getUser();// new User();
        $user->addRole("admin");
        //$user->setUsername("zhangbing");
        //$user->setFullName("zhangbing");
        //$user->setEmail("abc@abc.com");
        //$view = $this->view($user, Response::HTTP_OK);
        //return $this->handleView($view);
        return $this->handleResponse($user, $statusCode, [], $message);
    }

    /**
     * @param null $oldPassword
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return null
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }


    /**
     * Assign roles to special group.
     * @Rest\Post("/users/{id}/roles")
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function assignRolesToUserAction(Request $request, $id)
    {
        $statusCode = Response::HTTP_CREATED;
        $errorMessage ="";
        $arrRoles = [];
        /** @var User $user */
        $user = $this->getEntity($id);
        if(!$user){
            $errorMessage = "user not found";
        }
        $data = json_decode($request->getContent(), true);
        $postRoles= $data["roles"];
        if(0){

            $repository = $this->getDoctrine()->getRepository(Role::class);

            foreach($postRoles as $postRole){
                $roleEntity = $repository->findOneById($postRole["id"]);
                if($roleEntity){
                    $arrRoles[] = $roleEntity->getTag();
                }
            }
        }else{
            foreach($postRoles as $postRole){
                $arrRoles[] = $postRole["role_tag"];
            }
        }
        $user->setRoles($arrRoles);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->handleResponse($user, $statusCode, [], $errorMessage);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['security.password_encoder'] = UserPasswordEncoderInterface::class;

        return $subscribedServices;
    }
}