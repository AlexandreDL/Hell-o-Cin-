<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/*
 Le but de ce lister est de remplacer l'ajout du mot de passe tel qu'il est actuellement généré
 par un listener qui s'occupera de sa construction
*/
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $listener = function (FormEvent $event) {
            /*
                L'objet du type FormEvent contient deux methodes :
                - getData() qui permet de recuperer les données du formulaire
                - getForm() qui permet de recupèrer le formulaire en tant que tel
            */
            // Conditionner la construction du champs password sur selon le contexte de l'objet user (new OU update)
        };


        /*
         Actuellement je souhaite proposer un champs du type password pour mon mot de passe.

         Le soucis c'est qu'avec le type password mon mot de passe si non saisi est null voir chaine vide (selon les version de chez symfo)

         Ici, mon formulaire est utilisé dans 2 contexte différent :

            - l'ajout : je souhaiterai eventuellement avoir un mdp not blank -> ajout de constraint
            - la modification : je souhaiterai laisser l'oppotunité a mon utilisateur de ne pas devoir modifier ou resaisir son mot de passe a chaque utilisation => autoriser le fait qu'il puisse etre vide

            Actuellement je ne peux pas mettre une seule contrainte notblacnk sur ce champs , il existera a terme une solution alternative pour tester le contexte de notre formulaire
        
        */
        $builder
            ->add('username')

            /*
            Remplacer la construction champs password par la methode addEventListener() qui prend un parametre:
            - L'event (FormEvents) sur lequel doit s'effectuer l'action 
            - le listener créé
            */
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,

                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('email')
            //->add('isActive')
            ->add('role')
            //->add('roleJsonFormat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
