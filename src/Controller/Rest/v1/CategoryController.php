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
use FOS\RestBundle\Context\Context;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
     * @param View $view
     */
    public function SetSerializationContext(View $view){
        $serializationContext = $view->getContext();
        $serializationContext->addGroups(['normal','articles']);
        // $view->getContext()->addGroup('normal');
        // $view->getContext()->setAttribute(AbstractNormalizer::IGNORED_ATTRIBUTES,['children','articles','password']);

        // all callback parameters are optional (you can omit the ones you don't use)
        $dateTimeToString = function ($dateTime) {
            return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';
        };

        $callBacks = [
            'publishedAt' => $dateTimeToString,
        ];

        $serializationContext->setAttribute( AbstractNormalizer::CALLBACKS, $callBacks);
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
        return $this->deleteEntity($id);
    }
}