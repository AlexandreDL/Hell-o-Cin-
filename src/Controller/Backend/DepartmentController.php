<?php

namespace App\Controller\Backend;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
 Recap : 

 Pour la BDD on sépare totu ceci en 3 elements
 - Entity = structure  => c'est la classe qui nous permettra de retrouver nos objet structuré sur le format de notre table presente coté SQL
 - Repository = interrogation de la BDD => permet d'effectuer toutes les requetes necessaires (+ custom) sur la table concerné par l'entity associé
 - EntityManger = modifie la BDD => permet d'effectuer toutes les modification / creation / suppression necesaire dans la BDD.

 Note: L'entity manager connait aussi l'etat de nos données en BDD 

*/


/**
 * 
 * @Route("/backend/department/", name="backend_department_")
 */
class DepartmentController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(DepartmentRepository $repository)
    {
        $departments = $repository->findAll();

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("{id}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Department $department = null)
    {
        /*
         Pour pouvoir gerer une erreur custom , il faut prevoir comme valeur par defaut d'entrée
         du null. Ainsi l'objet sera du type l'entité souhaitée OU null.
         De ce fait je peux facilement effectuer un traitement supplementaire (envoyer une erreur custom par ex)
         si je n'ai pas de departement dans mon cas.
        */
        if (!$department) {
            throw $this->createNotFoundException('Département introuvable');
        }
        
        return $this->render('backend/department/show.html.twig', [
            'department' => $department,
        ]);
    }

     /**
     * @Route("delete/{id}", name="delete", methods={"POST","GET"}, requirements={"id"="\d+"})
     */
    public function delete(Department $department, EntityManagerInterface $em)
    {
        $em->remove($department);
        $em->flush();

        $this->addFlash(
            'danger',
            'Suppression effectuée'
        );

        /*
          pour la majorité des actions a realiser en BDD => redirection au final sur la page permttant d'afficher soit la fonction prevue a cet effet
        */
        return $this->redirectToRoute('backend_department_index');
    }

    /**
     * Note : il est aussi possibile de mettre 2 routes différente pour une meme fonction
     * cela permet d'avoir des url plus correcte pour la semantique (SEO)
     * 
     * @Route("editcreate/{id}", name="edit_or_create", methods={"GET","POST"}, requirements={"id"="\d+|new"})
     */
    public function editOrCreate($id,Department $department = null, Request $request, EntityManagerInterface $em)
    {   
        //test si l'on arrive par le lien de creation et non de modification 
        $flashMessageType = 'info';
        $flashMessage = 'Mise à jour effectuée';

        //si mon departement est null => possibilité que cela soit pour un ajout car id va prendre la valeur "new"
        if(is_null($department)){

            if (!$id || $id != 'new') { //si department = null mais qu'id est présent => id n'est pas trouvé en BDD
                throw $this->createNotFoundException('Département introuvable');
            }

            //objet a remplir par handlerequest
            $department = new Department();

            $flashMessageType = 'success';
            $flashMessage = 'Enregistrement effectué';
        }

        $form = $this->createForm(DepartmentType::class, $department);

        //met a jour non seulement la variable formulaire + l'objet associé ($department)
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($department);
            $em->flush();

            $this->addFlash(
                $flashMessageType,
                $flashMessage
            );

            return $this->redirectToRoute('backend_department_index');
        }

        return $this->render('backend/department/edit_or_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
