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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\ApiControllerEvent;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
abstract class AbstractBaseApiController extends AbstractFOSRestController implements AbstractBaseApiControllerInterface
{

    var $entityClassName = "";

    /**
     * @var \ReflectionClass
     */
    var $rcEntity = null;

    var $entityFormTypeClassName = "";

    /**
     * @var \ReflectionClass
     */
    var $rcEntityFormType = null;
    var $errors = "";

    /**
    public function __construct(EventDispatcherInterface $dispatcher)
    {
    $this->dispatcher = $dispatcher;
    }
    */

    public function setEntityClass($entityClassName)
    {
        if (!empty($entityClassName) && class_exists($entityClassName)) {
            $this->entityClassName = $entityClassName;
            /**
             * 利用反射api构造一个控制器类对应的反射类
             */
            $this->rcEntity = new \ReflectionClass($entityClassName);
        }
    }

    private function getEntityClass()
    {
        if (empty($this->className)) {
            ; //throw
        }
        return $this->className;
    }

    public function setEntityFromTypeClass($entityFormTypeClassName)
    {
        if (!empty($entityFormTypeClassName) && class_exists($entityFormTypeClassName)) {
            $this->entityFormTypeClassName = $entityFormTypeClassName;
            /**
             * 利用反射api构造一个控制器类对应的反射类
             */
            $this->rcEntityFormType = new \ReflectionClass($entityFormTypeClassName);
        }
    }

    public function entityPrePersistOnAction($object)
    {

    }

    private function getEntityFromTypeClass()
    {
        $entityFormTypeClass = null;
        if ($this->rcEntityFormType) {
            $entityFormTypeClass = $this->rcEntityFormType->getName();
        }
        return $entityFormTypeClass;
    }

    /**
     * @param $entity
     * @return FormInterface|null
     */
    private function createEntityForm($entity)
    {
        $form = null;
        $formTypeClass = $this->rcEntityFormType->getName();
        if (!empty($formTypeClass)) {
            $form = $this->createForm($formTypeClass, $entity);
            if (!$form) {
                $this->errors = "create entity form fail";
            }
        } else {
            $this->errors = "not set valid entity form type class";
        }

        return $form;
    }

    private function createEntityObject()
    {
        if ($this->rcEntity) {
            $object = $this->rcEntity->newInstance();
        }
        return $object;
    }

    protected function getEntityRepository()
    {
        $repository = null;
        if ($this->rcEntity) {
            $class = $this->rcEntity->getName();
            $repository = $this->getDoctrine()->getRepository($class);
        }
        return $repository;
    }

    /**
     * @param View $view
     */
    public function SetSerializationContext(View $view)
    {

    }

    /**
     * Converts view into a response object.
     *
     * Not necessary to use, if you are using the "ViewResponseListener", which
     * does this conversion automatically in kernel event "onKernelView".
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @param string $message
     *
     * @return Response
     */
    protected function handleResponse($data = null, $statusCode = null, array $headers = [], $message = "")
    {
        $viewData = [
            "data" => $data,
            "message" => $message,
            //"status" => "ok"
        ];
        $view = $this->view($viewData, $statusCode, $headers);
        $this->SetSerializationContext($view);
        return parent::handleView($view);
    }


    /**
     * get a Lists of all category Objects.
     *
     * @return Response
     */
    public function getEntitiesAction()
    {
        $entities=[];
        $statusCode = Response::HTTP_OK;
        $message = "";
        try{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
            $repository = $this->getEntityRepository();
            $entities = $repository->findAll();
        }catch(\Exception $e){
            $message = $e->getMessage();
        }

        return $this->handleResponse($entities, $statusCode, [], $message);
    }

    /**
     * Create Entity object.
     *
     * @param Request $request
     * @return Response
     */
    public function createEntityAction(Request $request)
    {
        $message = "";
        $statusCode = Response::HTTP_CREATED;
        $object = $this->createEntityObject();
        $form = $this->createEntityForm($object);

        if ($form) {
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isValid()) {
                // call entityPrePersistOnAction
                $this->entityPrePersistOnAction($object);
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($object);
                    $em->flush();
                } catch (\Exception $e) {
                    if ($e instanceof UniqueConstraintViolationException) {
                        $object = null;
                        $message = "Unique Constraint Violation Exception";
                    }
                }
            } else {
                $object = null;
                $message = $form->getErrors();
            }
            unset($form);
        } else {
            $object = null;
            $message = "form create failed";
        }
        return $this->handleResponse($object, $statusCode, [], $message);
    }

    /**
     * Modify a specified Entity object by id and content.
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function modifyEntity(Request $request, int $id)
    {
        $message = "";
        $statusCode = Response::HTTP_CREATED;
        $repository = $this->getEntityRepository();
        $object = $repository->findOneById($id);
        $dispatcher = $this->get('event_dispatcher');

        if (!$object) {
            $message = 'Category with id ' . $id . ' does not exist!';
            // throw new EntityNotFoundException('Category with id ' . $id . ' does not exist!');
        } else {
            $form = $this->createEntityForm($object);

            if ($form) {
                $data = json_decode($request->getContent(), true);
                // 发送一个即将更新的事件
                if ($dispatcher->hasListeners(ApiControllerEvent::PRE_POST_PUT)) {
                    $event = new ApiControllerEvent($this, $object);
                    $dispatcher->dispatch($event, ApiControllerEvent::PRE_POST_PUT);
                    $object = $event->getData();
                    unset($event);
                }

                $form->submit($data);
                if ($form->isValid()) {
                    // call entityPrePersistOnAction
                    $this->entityPrePersistOnAction($object);
                    $em = $this->getDoctrine()->getManager();

                    // 发送一个即将入库的事件
                    if ($dispatcher->hasListeners(ApiControllerEvent::POST_PUT)) {
                        $event = new ApiControllerEvent($this, $object);
                        $dispatcher->dispatch($event, ApiControllerEvent::POST_PUT);
                        $object = $event->getData();
                        unset($event);
                    }

                    try {
                        $em->flush();
                        // 发送一个即将入库的事件
                        if ($dispatcher->hasListeners(ApiControllerEvent::POST_POST_PUT)) {
                            $event = new ApiControllerEvent($this, $object);
                            $dispatcher->dispatch($event, ApiControllerEvent::POST_POST_PUT);
                            $object = $event->getData();
                            unset($event);
                        }
                    } catch (\Exception $e) {
                        if ($e instanceof NotNullConstraintViolationException) {
                            $message = $e->getMessage();
                        }

                    }
                }else{
                    $message = $form->getErrors();
                }
                unset($form);
            }else{
                $object = null;
                $message = "form create failed";
            }
        }
        return $this->handleResponse($object, $statusCode, [], $message);
    }

    /**
     * Retrieves an Category resource
     *
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function getEntity(int $id) //: View
    {
        $repository = $this->getEntityRepository();
        $object = $repository->findOneById($id);
        if (!$object) {
            throw new EntityNotFoundException('Category with id ' . $id . ' does not exist!');
        }
        return $object;
    }

    /**
     * Retrieves an Category resource
     *
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function getEntityAction(int $id) //: View
    {
        $message = "";
        $statusCode = Response::HTTP_OK;

        $object = $this->getEntity($id);
        if (!$object) {
            $message = 'entity with id ' . $id . ' does not exist!';
            // throw new EntityNotFoundException('entity with id ' . $id . ' does not exist!');
        }

        return $this->handleResponse($object, $statusCode, [], $message);
    }

    /**
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteEntity(int $id) //: View
    {
        $message = "";
        $statusCode = Response::HTTP_NO_CONTENT;

        $object = $this->getEntity($id);
        if (!$object) {
            $message = 'entity with id ' . $id . ' does not exist!';
            // throw new EntityNotFoundException('Category with id ' . $id . ' does not exist!');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->handleResponse([], $statusCode, [], $message);
    }

    public function addEventListener($eventName, $listener, $priority = 0)
    {
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->addListener($eventName, $listener, $priority);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['event_dispatcher'] = EventDispatcherInterface::class;

        return $subscribedServices;
    }
}