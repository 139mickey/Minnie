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
     * @param View $view
     */
    public function SetSerializationContext(View $view){
        $serializationContext = $view->getContext();
        $serializationContext->addGroup('normal');
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

    public function entityPrePersistOnAction($object){
        /** @var Article $object */
        $object->setAuthor($this->getUserT());
        /** @var Slugger $slugger */
        $slugger = $this->get("slug");
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

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['slug'] = Slugger::class;

        return $subscribedServices;
    }
}