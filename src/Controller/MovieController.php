<?php
namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MovieController extends ApiController
{
    /**
     * @Route("/movies", methods="GET")
     */
    public function index(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->transformAll();
        /*
        $movies = array(); //$movieRepository->transformAll();
        $movie = array(
            array(
                "id" => 1,
                "title" => "zhangbing",
                "count" => 5,
            ), array(
                "id" => 2,
                "title" => "zhangruixin",
                "count" => 3,
            ), array(
                "id" => 3,
                "title" => "jiameini",
                "count" => 8,
            ), array(
                "id" => 4,
                "title" => "mini",
                "count" => 5,
            ), array(
                "id" => 5,
                "title" => "abc",
                "count" => 3,
            ));

        array_push($movies, $movie);
        */
        //print_r($movies);
        return $this->respond($movies);
    }

    /**
     * @Route("/movies", methods="POST")
     */
    public function create(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);

        if (!$request) {
            return $this->respondValidationError('Please provide a valid request!');
        }

        // validate the title
        if (!$request->get('title')) {
            return $this->respondValidationError('Please provide a title!');
        }

        // persist the new movie
        $movie = new Movie;
        $movie->setTitle($request->get('title'));
        $movie->setCount(0);
        $em->persist($movie);
        $em->flush();

        return $this->respondCreated($movieRepository->transform($movie));
    }

    /**
     * @Route("/movies/{id}/count", methods="POST")
     */
    public function increaseCount($id, EntityManagerInterface $em, MovieRepository $movieRepository)
    {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->respondNotFound();
        }

        $movie->setCount($movie->getCount() + 1);
        $em->persist($movie);
        $em->flush();

        return $this->respond([
            'count' => $movie->getCount()
        ]);
    }
}