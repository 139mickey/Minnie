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
class RolesController extends AbstractBaseApiController {
    var $entities;
    public function __construct()
    {
        $this->setEntityClass(Group::class);
        $this->setEntityFromTypeClass(GroupType::class);

        $this->entities = array(
            "1"=>array("module_name"=>"system", "module_id"=>1, "id"=>1, "role_name"=>"管理员","role_tag"=>"ROLE_ADMIN"),
            "2"=>array("module_name"=>"system", "module_id"=>1, "id"=>2, "role_name"=>"用户管理","role_tag"=>"BLOG_USER_ADMIN"),
            "3"=>array("module_name"=>"system", "module_id"=>1, "id"=>3, "role_name"=>"组管理员","role_tag"=>"GROUP_OTHER_ADMIN"),
            "4"=>array("module_name"=>"blog",   "module_id"=>2, "id"=>4, "role_name"=>"blog管理员","role_tag"=>"ROLE_BLOG_ADMIN"),
            "5"=>array("module_name"=>"cms",    "module_id"=>3, "id"=>5, "role_name"=>"cms管理员","role_tag"=>"ROLE_CMS_ADMIN"),
        );

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
     * Get all roles list.
     *
     * @Rest\Get("/roles")
     *
     * @return Response
     */
    public function getAction()
    {
        $statusCode = Response::HTTP_OK;
        return $this->handleResponse($this->entities, $statusCode);
    }

    /**
     * Retrieves an Category resource
     *
     * @Rest\Get("/roles/{mid}")
     * @param int $mid
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction($mid) //: View
    {
        $data =[];
        $statusCode = Response::HTTP_OK;

        // $repository = $this->getEntityRepository();
        foreach($this->entities as $key=>$entity){
            if($entity["module_id"] == $mid){
                $data[$key] = $entity;
            }
        }
        return $this->handleResponse($data, $statusCode);
    }
}
