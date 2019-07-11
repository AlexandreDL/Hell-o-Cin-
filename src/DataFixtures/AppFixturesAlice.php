<?php

use Nelmio\Alice\Loader\NativeLoader;

class AppFixtures extends Fixture
{

    /*
     Lorsque j'utilise alice , la fonction load n'est pas completement la meme
     je dois aller charger le fichier de configuration yaml pour le faire analyser par alice 
     
     */
    public function load(ObjectManager $em)
    {
        $loader = new NativeLoader();
        
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures_anais.yml')->getObjects();
        $entities = $loader->loadFile(__DIR__.'/fixtures_maria.yml')->getObjects();
        
        //empile la liste d'objet à enregistrer en BDD
        foreach ($entities as $entity) {
            $em->persist($entity);
        };
        
        //enregistre
        $em->flush();
    }
}