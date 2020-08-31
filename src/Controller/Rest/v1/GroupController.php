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

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use App\Event\ApiControllerEvent;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Context\Context;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Orm\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class GroupController extends AbstractBaseApiController {
    public function __construct()
    {
        $this->setEntityClass(Group::class);
        $this->setEntityFromTypeClass(GroupType::class);
    }

    /**
     * @param View $view
     */
    public function SetSerializationContext(View $view){
        /** @var Context $serializationContext */
        $serializationContext = $view->getContext();
        $serializationContext->addGroup('normal');
    }

    /**
     * Get all groups list.
     * @Rest\Get("/groups")
     *
     * @return Response
     */
    public function getAction()
    {
        return $this->getEntitiesAction();
    }

    public function entityPrePersistOnAction($object){

    }

    /**
     * Create a new group.
     * @Rest\Post("/groups")
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
     * Modify a specified Entity object Provide all information about the object.
     * @Rest\Put("/groups/{id}")
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function putAction(Request $request, int $id)
    {
        return $this->modifyEntity($request,$id);
    }
    
    /**
     * Modify a specified Entity object Provide some information about the object.
     * @Rest\Patch("/groups/{id}")
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function patchAction(Request $request, int $id)
    {
        return $this->modifyEntity($request,$id);
    }
    
    /**
     * Retrieves an Category resource
     *
     * @Rest\Get("/groups/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction(int $id) //: View
    {
        return $this->getEntityAction($id);
    }

    /**
     * @Rest\Delete("/groups/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteAction(int $id) //: View
    {
        return $this->deleteEntity($id);
    }

    /**
     * Get all users of special group.
     * @Rest\Get("/groups/{id}/users")
     *
     * @param $id
     * @return Response
     */
    public function getUserOfGroupAction($id)
    {
        $statusCode = Response::HTTP_OK;
        $message ="";
        /** @var Group $group */
        $group = $this->getEntity($id);
        if(!$group){
            $message = "group not found";
        }
        $users = $group->getUsers();
        return $this->handleResponse($users, $statusCode, [], $message);
    }

    /**
     * Add a user to special group.
     * @Rest\Post("/groups/{id}/users")
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function addUserToGroupAction(Request $request, $id)
    {
        $statusCode = Response::HTTP_CREATED;
        $message ="";
        /** @var Group $group */
        $group = $this->getEntity($id);
        if(!$group){
            $message = "group not found";
        }
        $data = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneById($data["id"]);
        $group->addUser($user);
        return $this->handleResponse($user, $statusCode, [], $message);
    }


    /**
     * remove a user from special group.
     * @Rest\Delete("/groups/{gid}/users/{uid}")
     *
     * @param integer $gid
     * @param integer $uid
     * @return Response
     */
    public function removeUserFromGroupAction($gid, $uid)
    {
        $statusCode = Response::HTTP_NO_CONTENT;
        $message ="";
        /** @var Group $group */
        $group = $this->getEntity($gid);
        if(!$group){
            $message = "group not found";
        }
        //$data = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneById($uid);
        if(!$user){
            $message = "user not found";
        }
        $group->removeUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->handleResponse([], $statusCode, [], $message);
    }

    /**
     * remove a user from special group.
     * @Rest\Delete("/groups/{id}/users")
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function removeUsersFromGroupAction(Request $request, $id)
    {
        $statusCode = Response::HTTP_NO_CONTENT;
        $message ="";
        /** @var Group $group */
        $group = $this->getEntity($id);
        if(!$group){
            $message = "group not found";
        }
        // Get IDs to be deleted,like ["user1", "user2", "user3"]
        $data = json_decode($request->getContent(), true);

        try{
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            /** @var Query $query */
            $rsm = new ResultSetMapping();
            $query = $em->createNativeQuery('DELETE FROM sys_user_group WHERE user_id in (:ids)', $rsm);
            $query->setParameter(':ids', $data["ids"], Connection::PARAM_INT_ARRAY);
//            $sql = $query->getSQL();
            $result = $query->getResult();
      }catch (\Exception $e){
            $message = $e->getMessage();
        }
        return $this->handleResponse([$result], $statusCode, [], $message);
    }

    /**
     * Assign roles to special group.
     * @Rest\Post("/groups/{id}/roles")
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function assignRolesToGroupAction(Request $request, $id)
    {
        $statusCode = Response::HTTP_CREATED;
        $message ="";
        $arrRoles = [];
        /** @var Group $group */
        $group = $this->getEntity($id);
        if(!$group){
            $message = "group not found";
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
        $group->setRoles($arrRoles);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->handleResponse($group, $statusCode, [], $message);
    }
}
