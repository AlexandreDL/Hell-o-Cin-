<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Job;
use App\Entity\Role;
use App\Entity\Team;

use App\Entity\User;
use App\Utils\Slugger;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use App\DataFixtures\Faker\MovieAndGenreProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /*
     Le conteneur de servive se charge d'instancier les service et peur leur passer (moyennant une configuration plus poussé) des parametre.

     Puisque le conteneur de service n'a acces qu'a l'instance des classe / service , celui ci ne peux injecter des dépendances (objet instancié) uniquement par le constructeur.

     Seul les fichier present dans le dossier src/Controller présente une exeption a savoir que l'injection est possible directement dans une methode de controller sans passer par un constructeur
    */

    private $passwordEncoder;
    private $slugger;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Slugger $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $roleAdmin = new Role();
        $roleAdmin->setCode('ROLE_ADMIN');
        $roleAdmin->setName('Admin');

        $roleUser = new Role();
        $roleUser->setCode('ROLE_USER');
        $roleUser->setName('Utilisateur');

        $userAdmin = new User();
        $userAdmin->setEmail('admin@o.com');
        $userAdmin->setUsername('admin');

        $encodedPassword = $this->passwordEncoder->encodePassword($userAdmin, 'admin');
        $userAdmin->setPassword($encodedPassword);

        $userAdmin->setRoleJsonFormat('ROLE_ADMIN');
        $userAdmin->setRole($roleAdmin);

        $userUser = new User();
        $userUser->setEmail('user@o.com');
        $userUser->setUsername('user');

        $encodedPassword = $this->passwordEncoder->encodePassword($userUser, 'user');
        $userUser->setPassword($encodedPassword);

        $userUser->setRoleJsonFormat('ROLE_USER');
        $userUser->setRole($roleUser);
        
        //attention a bien persister les objet associé avant l'enregistrement des user sinon doctrine ne pourra pas  effectuer la liaison avec une donnée qui n'existe pas
        $manager->persist($roleUser);
        $manager->persist($roleAdmin);
        $manager->persist($userAdmin);
        $manager->persist($userUser);

        $generator = Factory::create('fr_FR');

        //ajout provider custom MovieAndGenreProvider 
        //Note : $generator est attendu dans le constructeur de la classe Base de faker
        $generator->addProvider(new MovieAndGenreProvider($generator));

        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);
        
        /*
         Faker n'apelle pas le constructeur d'origine donc genres n'est pas setté
         -> effet de bord sur adders qui utilise la methode contains sur du null
        */

        /*
        addentity prend 4 parametres:
        - le namespace de l'entité a générer
        - le nombre d'entité a générer souhaité
        - le contenu a generer pour les propriété souhaité  stocké dans un tableau
        - (optionnel) le code a executer juste apres la generation des propriété par faker  => utile lorsque je souhaite modifier a la volée une donnée qui vient d'etre générée par faker
        */
        $populator->addEntity('App\Entity\Movie', 10, array(
            'title' => function() use ($generator) { return $generator->unique()->movieTitle(); },
            'score' => function() use ($generator) { return $generator->numberBetween(0, 5); },
            'summary' => function() use ($generator) { return $generator->paragraph(); },
            'poster' => '',
            //'slug' => function() use ($generator) { return $generator->unique()->movieTitle(); }, // non valide : car ne permet pas d'obtenir le titre généré pour l'objet en cours 
             
        ),//suite a cette generation , je peux dire a faker de passer l'objet en cours de creation  aux fonctions de callback
        [ //fonctions de callback (appels-sur-le-retour)
            function($rocketMovie) { //passe l'objet en cours de generation ici un movie
                $title = $rocketMovie->getTitle(); 
                $sluggifiedTitle = $this->slugger->slugify($title);
                $rocketMovie->setSlug($sluggifiedTitle);
            },
        ]
       );
            
        $populator->addEntity('App\Entity\Genre', 20, array(
            'name' => function() use ($generator) { return $generator->unique()->movieGenre(); },
        ));

        $populator->addEntity('App\Entity\Person', 20, array(
            'name' => function() use ($generator) { return $generator->name(); },
        ));
        
        $populator->addEntity('App\Entity\Casting', 50, array(
            'orderCredit' => function() use ($generator) { return $generator->numberBetween(1, 10); },
            'role' => function() use ($generator) { return $generator->firstName(); },
        ));
        
        $populator->addEntity(Department::class, 50, array(
            'name' => function() use ($generator) { return $generator->company(); },
        ));

        $populator->addEntity(Job::class, 50, array(
            'name' => function() use ($generator) { return $generator->jobTitle(); },
        ));

        $populator->addEntity(Team::class, 150);

        $inserted = $populator->execute();

        //generated lists
        $movies = $inserted['App\Entity\Movie'];
        $genres = $inserted['App\Entity\Genre'];

        foreach ($movies as $movie) {

            shuffle($genres);

            // tableau rand en amont => recuperation des 3 premiers donne une valeur unique par rapport a mt rand
            $movie->addGenre($genres[0]);
            $movie->addGenre($genres[1]);
            $movie->addGenre($genres[2]);

            $manager->persist($movie);
        }
        $manager->flush();
    }
}
