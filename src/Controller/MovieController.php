<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Casting;

use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_index", methods={"GET","POST"})
     */
    public function index(Request $request)
    {   
         $repository = $this->getDoctrine()->getRepository(Movie::class);
         
         /*
          Note: recuperer un parametre en 

          POST : $request->request->get('moninput');
          GET : $request->query->get('title');
         */
         $searchTitle = $request->request->get('title'); //par defaut si n'existe pas renvoit du null

         if($searchTitle){

            $movies = $repository->findByTitle($searchTitle);
 
         } else {
             //Query builder
            $movies = $repository->findAllQueryBuilderOrderedByName();
         }

         $lastMovies = $repository->lastRelease(10);

        return $this->render('movie/index.html.twig',[
            'movies' => $movies,
            'last_movies' => $lastMovies,
            'searchTitle' => $searchTitle
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show", methods={"GET"})
     */
    public function show(Movie $movie, CastingRepository $castingRepository)
    {   
        $castings = $castingRepository->findByMovieQueryBuilder($movie);

        return $this->render('movie/show.html.twig',[
            'movie' => $movie,
            'castings' => $castings
        ]);
    }
}
