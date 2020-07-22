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
/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
abstract class AbstractBaseApiController extends AbstractFOSRestController implements AbstractBaseApiControllerInterface{

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


    public function setEntityClass($entityClassName){
        if(!empty($entityClassName) && class_exists($entityClassName)) {
            $this->entityClassName = $entityClassName;
            /**
             * 利用反射api构造一个控制器类对应的反射类
             */
            $this->rcEntity = new \ReflectionClass($entityClassName);
        }
    }

    private function getEntityClass(){
        if(empty($this->className)){
            ;//throw
        }
        return $this->className;
    }

    public function setEntityFromTypeClass($entityFormTypeClassName){
        if(!empty($entityFormTypeClassName) && class_exists($entityFormTypeClassName)) {
            $this->entityFormTypeClassName = $entityFormTypeClassName;
            /**
             * 利用反射api构造一个控制器类对应的反射类
             */
            $this->rcEntityFormType = new \ReflectionClass($entityFormTypeClassName);
        }
    }

    public function entityPrePersistOnPostAction($object){

    }

    private function getEntityFromTypeClass(){
        $entityFormTypeClass = null;
        if($this->rcEntityFormType){
            $entityFormTypeClass = $this->rcEntityFormType->getName();
        }
        return $entityFormTypeClass;
    }

    private function createEntityForm($entity){
        $form = null;
        $formTypeClass =  $this->rcEntityFormType->getName();
        if(!empty($formTypeClass)){
            $form = $this->createForm($formTypeClass, $entity);
            if(!$form){
                $this->errors = "create entity form fail";
            }
        }else{
            $this->errors = "not set valid entity form type class";
        }

        return $form;
    }

    private function createEntityObject(){
        if($this->rcEntity) {
            $object = $this->rcEntity->newInstance();
        }
        return $object;
    }

    private function getEntityRepository(){
        $repository = null;
        if($this->rcEntity){
            $class = $this->rcEntity->getName();
            $repository = $this->getDoctrine()->getRepository($class);
        }
        return $repository;
    }

    /**
     * @param View $view
     */
    public function needContextGroup(View $view){

    }

    /**
     * get a Lists of all category Objects.
     *
     * @return Response
     */
    public function getEntitiesAction()
    {
        $repository = $this->getEntityRepository();
        $entities = $repository->findAll();

        $view = $this->view($entities, Response::HTTP_OK);
        $this->needContextGroup($view);
        // $view->getContext()->addGroup('normal');
        // $view->getContext()->setAttribute(AbstractNormalizer::IGNORED_ATTRIBUTES,['children','articles','password']);
        return $this->handleView($view);
    }

    /**
     * Create Entity object.
     *
     * @param Request $request
     * @return Response
     */
    public function createEntityAction(Request $request)
    {
        $object = $this->createEntityObject();
        $form = $this->createEntityForm($object);

        if($form){
            $data = json_decode($request->getContent(), true);
            $form->submit($data);

            if ($form->isSubmitted() && $form->isValid()) {

                // call entityPrePersistOnPostAction
                $this->entityPrePersistOnPostAction($object);
                $em = $this->getDoctrine()->getManager();
                $em->persist($object);
                $em->flush();
                unset($form);
                return $this->handleView($this->view(['data'=>$object, 'status' => 'ok'], Response::HTTP_CREATED));
            }
            $errors = $form->getErrors();
            unset($form);
            return $this->handleView($this->view($errors));
        }
        return $this->handleView($this->view($this->error));
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
        $repository = $this->getEntityRepository();
        $object = $repository->findOneById($id);
        if (!$object) {
            throw new EntityNotFoundException('Category with id '.$id.' does not exist!');
        }

        $form = $this->createEntityForm($object);

        if($form){
            $data = json_decode($request->getContent(), true);
            $form->submit($data);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                unset($form);
                return $this->handleView($this->view(['data'=>$object, 'status' => 'ok'], Response::HTTP_CREATED));
            }

            $errors = $form->getErrors();
            unset($form);
            return $this->handleView($this->view($errors));
        }
        return $this->handleView($this->view($this->errors));
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
        $repository = $this->getEntityRepository();
        $object = $repository->findOneById($id);
        if (!$object) {
            throw new EntityNotFoundException('Category with id '.$id.' does not exist!');
        }

        // In case our GET was a success we need to return a 200 HTTP OK response with the request object
        $view = $this->view($object, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteEntity(int $id) //: View
    {
        $repository = $this->getEntityRepository();
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
    }
}