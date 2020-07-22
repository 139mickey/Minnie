<?php


/*
 * This file is part of the ModuleBundle package.
 *
 * (c); Zhang Bing qq:550695
 */

namespace App\Controller\Rest\v1;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

/**
 *
 * @author Zhang Bing <550695@qq.com>
 */

interface AbstractBaseApiControllerInterface
{

    /**
     * @param $entityClassName
     * @return mixed
     */
    public function setEntityClass($entityClassName);

    /**
     * @param $entityFormTypeClassName
     * @return mixed
     */
    public function setEntityFromTypeClass($entityFormTypeClassName);


    /**
     * get a Lists of all category Objects.
     *
     * @return Response
     */
    public function getEntitiesAction();


    /**
     * Create Entity object.
     *
     * @param Request $request
     * @return Response
     */
    public function createEntityAction(Request $request);


    /**
     * Modify a specified Entity object by id and content.
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function modifyEntity(Request $request, int $id);


    /**
     * Retrieves an Category resource
     *
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function getEntityAction(int $id);


    /**
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteEntity(int $id);

    /**
     * @param View $view
     */
    public function needContextGroup(View $view);
}
