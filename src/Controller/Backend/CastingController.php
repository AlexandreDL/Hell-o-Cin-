<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/casting", name="backend_")
 */
class CastingController extends AbstractController
{
    /**
     * @Route("/movie/{id}", name="casting_index", methods={"GET"})
     */
    public function index(Movie $movie): Response
    {
        /*
         Dans cette version je souhaite filtrer mes castings par film.

         le fait de passer le film dans l'url me permet de recuperer un movie mais aussi ses données associées.

         de ce fait ma vue original étant deja prevue pour afficher une liste de casting , remplacer cette liste complete par celle du film passé en parametre ne change aboslument rien.

         neanmoins , je vais devoir aussi passer le film pour recuperer des information supplémentaires afin de generer notamment les lien et le titre du film.
        
        */
        return $this->render('backend/casting/index.html.twig', [
            'movie' => $movie,
        ]);
    }

    // ANCIENNE VERSION
    /*public function index(CastingRepository $castingRepository): Response
    {
        return $this->render('backend/casting/index.html.twig', [
            'castings' => $castingRepository->findAll(),
        ]);
    }*/

    /**
     * @Route("/new/movie/{id}", name="casting_new", methods={"GET","POST"})
     */
    public function new(Request $request, Movie $movie): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dorénavant , le movie n'apparaissant plus dans le formulaire doit etre directement associé dans le casting concerné
            //je le recupere via l'id passé en parametre
            $casting->setMovie($movie);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($casting);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            //attention , ici cette a changé car elle prend l'id en parametre , je l'ajoute aussi a la redirection
            return $this->redirectToRoute('backend_casting_index', ['id' => $movie->getId()]);
        }

        return $this->render('backend/casting/new.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
            'movie' => $movie
        ]);
    }

    /**
     * @Route("/{id}", name="casting_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Casting $casting): Response
    {
        return $this->render('backend/casting/show.html.twig', [
            'casting' => $casting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="casting_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Casting $casting): Response
    {
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_casting_index', [
                'id' => $casting->getId(),
            ]);
        }

        return $this->render('backend/casting/edit.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Casting $casting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$casting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $movieId = $casting->getMovie()->getId();
            $entityManager->remove($casting);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_casting_index', ['id' => $movieId]);
    }
}
