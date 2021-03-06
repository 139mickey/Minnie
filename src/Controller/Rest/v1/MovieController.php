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

use App\Entity\Movie;
use App\Form\MovieType;
/**
 * Make the BASE URl of the API looks like http://xxx.xxx/api/v1/
 * R o u t e("/api", name="api_")
 * @Route("/v1", name="api_")
 */
class MovieController extends AbstractFOSRestController {

    /**
     * Lists all Objects.
     * @Rest\Get("/movies")
     *
     * @return Response
     */
    public function getAction()
    {

        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $entities = $repository->findAll();

        $view = $this->view($entities, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * Create Entity object.
     * @Rest\Post("/movies")
     *
     * @param Request $request
     * @return Response
     */
    public function postObjectAction(Request $request)
    {
        $object = new Movie();
        $form = $this->createForm(MovieType::class, $object);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();
            return $this->handleView($this->view(['data'=>$object, 'status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Modify a specified Entity object by id and content.
     * @Rest\Put("/movies/{id}")
     *
     * @param Request $request
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function putObjectAction(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $object = $repository->findOneById($id);
        if (!$object) {
            throw new EntityNotFoundException('movie with id '.$id.' does not exist!');
        }

        $form = $this->createForm(MovieType::class, $object);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->handleView($this->view(['data'=>$object, 'status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Retrieves an Article resource
     *
     * @Rest\Get("/movies/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Response
     */
    public function RetrieveAction(int $id) //: View
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $object = $repository->findOneById($id);
        if (!$object) {
            throw new EntityNotFoundException('movie with id '.$id.' does not exist!');
        }

        // In case our GET was a success we need to return a 200 HTTP OK response with the request object
        $view = $this->view($object, Response::HTTP_OK);
        return $this->handleView($view);
    }


    /**
     * @Rest\Delete("/movies/{id}")
     * @param \App\Controller\Rest\v1\int|int $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return static
     */
    public function deleteObjectAction(int $id) //: View
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $object = $repository->findOneById($id);

        if (!$object) {
            throw new EntityNotFoundException('movie with id '.$id.' does not exist!');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        $view = $this->view([], Response::HTTP_NO_CONTENT);
        return $this->handleView($view);
    }
}