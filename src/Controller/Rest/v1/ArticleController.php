<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20-4-29
 * Time: 下午8:06
 */

namespace App\Controller\Rest\v1;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityNotFoundException;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

use App\Utils\Slugger;
use App\Entity\Article;
use App\Form\ArticleType;
/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class ArticleController extends AbstractBaseApiController {


    public function __construct()
    {
        $this->setEntityClass(Article::class);
        $this->setEntityFromTypeClass(ArticleType::class);
    }

    /**
     * get a Lists of all category Objects.
     * @Rest\Get("/articles")
     *
     * @return Response
     */
    public function getAction()
    {
        return $this->getEntitiesAction();
    }

    private function getUserT(){
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $object = $repository->findOneById(1);
        return $user;
    }

    public function entityPrePersistOnPostAction($object){
        /** @var Article $object */
        $object->setAuthor($this->getUserT());
        /** @var Slugger $slugger */
        $slugger = $this->get(Slugger::class);
        $object->setSlug($slugger->slugify($object->getTitle()));
    }
    /**
     * Create Entity object.
     * @Rest\Post("/articles")
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
     * @Rest\Put("/articles/{id}")
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
     * @Rest\Get("/articles/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction(int $id) //: View
    {
        return $this->getEntityAction($id);
    }

    /**
     * @Rest\Delete("/articles/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteAction(int $id) //: View
    {
        return $this->deleteEntity($id);
    }
}