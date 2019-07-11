<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @TODO : terminer de deplacement des fichier dans backend + les vue + renommage des liens 
 * @Route("/backend/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * 
     * Pour encoder un mot de passe , je doit injecter en tant que dependance UserPasswordEncoderInterface 
     * 
     * Celui va me permettre de recuperer l'encoodage definit sur l'entité user definie dans security.yml et de génerer un mot de passe encodé avec le nouveau mdp
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $passwordEncoder->encodePassword(
                 $user, #detecte le type d'encodage
                 $user->getPassword() #le mot de passse a encoder
            );

            //j'ecrase le mot de passe en clair par celui que je vient d'encoder
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $oldPassword = $user->getPassword();

        //Pour rappel : handlerequest met non seulement le formulaire mais l'objet user a jour
        // donc si je souhaite recuperer l'ancien mot de passe de mon utilisateur cela doit se faire avant
        $form->handleRequest($request); //ici le mot de passe est mis a jour a vide

        if ($form->isSubmitted() && $form->isValid()) {

            //si mon nouveau mot de passe est pas vide
            if(!is_null($user->getPassword())){

                $encodedPassword = $passwordEncoder->encodePassword(
                    $user, #detecte le type d'encodage
                    $user->getPassword() #le mot de passse a encoder
               );
               
            } else { // ci mot de passe est vide
                $encodedPassword = $oldPassword;
            }

            //j'ecrase le mot de passe en clair par celui que je vient d'encoder
            $user->setPassword($encodedPassword);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
