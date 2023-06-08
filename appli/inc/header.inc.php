<?php require_once 'init.inc.php'; //Inclusion du fichier init.inc.php  ?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Appli</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--  CDN de BOOTSTRAP -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- CDN FONT AWESOME-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

	<!-- CSS PERSO ( en dernière position ) -->
	<link rel="stylesheet" href="">
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="<?= URL ?>index.php">LOGO</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	      <li class="nav-item">
	        <a class="nav-link" href="<?= URL ?>index.php">Accueil</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="<?= URL ?>panier.php">Panier</a>
	      </li>

	    <?php if( userConnect() ) : //Si l'internaute est connecté, on affiche les liens 'profil' et 'deconnexion' ?>

			<li class="nav-item">
				<a class="nav-link" href="<?= URL ?>profil.php">Profil</a>
			</li>	      
			<li class="nav-item">
				<a class="nav-link" href="<?= URL ?>connexion.php?action=deconnexion">Deconnexion</a>
			</li>

	    <?php else : //SINON, c'est que l'on est pas connecté, on affiche les liens 'connexion' ?>

				<a class="nav-link" href="<?= URL ?>connexion.php">Connexion</a>
			</li>

	    <?php endif; ?>

	    <?php if( adminConnect() ) : //SI l'admin est connecté, on affiche les liens du backOffice ?>

	      <li class="nav-item dropdown">
	        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	          BackOffice
	        </a>
	        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
	          <a class="dropdown-item" href="<?= URL ?>admin/gestion_boutique.php">Gestion produits</a>
	          <a class="dropdown-item" href="<?= URL ?>admin/gestion_membre.php">Gestion des utilisateurs</a>
	          <a class="dropdown-item" href="<?php echo URL ?>admin/gestion_commande.php">Gestion des demandes</a>
	        </div>
	      </li>

	    <?php endif; ?>

	    </ul>
	  </div>
	</nav>

	<div class="container">