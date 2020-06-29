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
use Doctrine\ORM\EntityNotFoundException;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

use App\Entity\Category;
use App\Form\CategoryType;
/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class CategoryController extends AbstractBaseApiController {


    public function __construct()
    {
        $this->setEntityClass(Category::class);
        $this->setEntityFromTypeClass(CategoryType::class);
    }

    /**
     * get a Lists of all category Objects.
     * @Rest\Get("/categories")
     *
     * @return Response
     */
    public function getAction()
    {
        return $this->getEntitiesAction();
    }

    /**
     * Create Entity object.
     * @Rest\Post("/categories")
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        return $this->createEntityAction($request);
    }

    /**
     * Modify a specified Entity object by id and content.
     * @Rest\Put("/categories/{id}")
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
     * Retrieves an Category resource
     *
     * @Rest\Get("/categories/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction(int $id) //: View
    {
        return $this->getEntityAction($id);
    }


    /**
     * @Rest\Delete("/categories/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteAction(int $id) //: View
    {
/*
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $object = $repository->findOneById($id);

        if (!$object) {
            throw new EntityNotFoundException('Category with id '.$id.' does not exist!');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        $view = $this->view([], Response::HTTP_NO_CONTENT);
        return $this->handleView($view);
*/

        return $this->deleteEntity($id);
    }
}