<?php

namespace App\Form;

use App\Entity\Casting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', TextType::class, [
                'empty_data' => '', 
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min'        => 3,
                        'max'        => 100,
                        'minMessage' => 'Pas assez de caractères (min attendu : {{ limit }})',
                        'maxMessage' => 'Trop caractères (max attendu : {{ limit }})',
                    ])
                ]
            ])
            ->add('orderCredit', IntegerType::class, [
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('person')

            /*
             equivalent a : 

             ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'name'
            ])
            
            */

            // pour manipuler le type de liste possible checkbox, select etc : https://symfony.com/doc/current/reference/forms/types/choice.html#select-tag-checkboxes-or-radio-buttons
            //->add('movie') //cf casting à partir de movie

            /*
             Lorsque je souhaite afficher une liste déroulante avec le form de chez symfony

             celui ci va simplement traduire ma demande comme ceci pour twig
            
             <select>

             foreach($persons as $person){ 
                <option> echo $person </option> <--- Erreur car je ne peux pas faire un echo d'un objet
             }

            </select>
            
            __toString() permet sur lon entité lors d'un echo , de renvoyer un format textuel minimal pour indiquer ce qu'il y a dans mon objet .

            Dans les bonnes pratiques , penser a toujours mettre un tostring dans une entité voir une classe
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}