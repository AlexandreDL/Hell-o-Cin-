Merci maria <3

étape par étape : 
on avait un lien casting vers tous les castings pêle-mêle. 
je veux avoir les castings filtrés par film. 
- j'avais une fonction dans casting controller qui faisait un findAll. =>
je veux les castings liés au movie que je veux afficher. je modivie la route de index de Casting. j'ajoute movie et id dans l'url. Movie pour plus de cohérence et id en param. 
je dois modifier les boutons de lien. j'avais back to list mais je dois ajouter l'id du film en param. Pareil pour edit. 
de movie vers casting et en interne dans casting, pour arriver sur la bonne liste et pour atterir sur la bonne url. 
dans index, je return movie plutôt que casting et getCasting() pour pouvoir récup l'id du movie et son titre. 

Je dois aussi modif le template. Pour les liens et pour récup dans la boucle pas castings comme avant mais les castings du movie en cours. 
je pouvais choisir le film parmi tous les films. Je dois modifier ça car je veux pouvoir create new casting uniquement du current film. 
donc je commente le ->add('movie). Pour ce gaire, dans le template, j'ai rajouté dans la route new l'id du movie, pour pouvoir récup info dans l'url. => ... /new/movie/{id}
dans castingController, je dois modif fonction new. 
$casting->setMovie($movie)
je dois aussi modif redirection : je dois ajouter en param de route $movie->getId() car j'avais modifié cette route pour filtrer les castings. 

sur le formulaire de new : 
je dois modifier les liens dans les vues pour pouvoir rediriger au bon endroit. je vais devoir récup movie.id pour revenir à la liste des casting du movie. 
dans edit aussi je dois modifier les liens. 
idem dans show. 
Attention : aller voir dans paramConverter comment associer paramètre à la bonne entité. 
sinon, utiliser repository. 
problème lien de redirection de delte => modifier en rajoutant id dans param. Je vais devoir le récup plus haut que le return dans delete car dans le return il est déjà supprimé. 