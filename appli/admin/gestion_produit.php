<?php require_once '../inc/header.inc.php'; ?>
<?php
//Restriction de l'accès à la page administrative : 
if( !adminConnect() ){ //SI l'admin N'EST PAS connecté, on le redirige vers la page de connexion

	header('location:../connexion.php');
	exit();
}

//---------------------------------------------
//Gestion de la SUPPRESSION :
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'suppression'

	//récupération de la colonne 'photo' dans le table 'produit' a condition que 'lid_produit correponde à l'id passée dans l'URL
	$r = execute_requete(" SELECT photo FROM produit WHERE id_produits = '$_GET[id_produits]' ");

	$photo_a_supprimer = $r->fetch( PDO::FETCH_ASSOC );
		//debug( $photo_a_supprimer );

	$chemin_photo_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo_a_supprimer['photo'] );
		//debug( $chemin_photo_a_supprimer );

		//str_replace( arg1, arg2, arg3 ) : fonction interne de php qi permet de remplacer une chaine de caractères
			//arg1 : la chaine que l'on souhaite remplacer
			//arg2 : la chaine de remplacement
			//arg3 : Sur quelle chaine je veux effectuer les changements

		/*Ici, je remplace   : http://localhost
						par  : C:/xamp/htdocs (= $_SERVER['DOCUMENT_ROOT'])
						dans : $photo_a_supprimer['photo'] (= http://localhost/boutique/nom_photo.png : c'est le chemin de la photo en BDD)
		*/
	if( !empty( $chemin_photo_a_supprimer ) && file_exists( $chemin_photo_a_supprimer ) ){

		unlink( $chemin_photo_a_supprimer );
		//unlink( $url_fichier_a_supprimer ) : permet de supprimer un fichier
	}

	//Suppression dans la table 'produit' A CONDITION que l'id produit corresponde à l'id_produit que l'on récupère dans l'URL
	execute_requete(" DELETE FROM produit WHERE id_produit = '$_GET[id_produit]' ");
}

//---------------------------------------------
//Gestion des produits (INSERTION et MODIFICATION) :
if( !empty( $_POST ) ){ //SI le formulaire a été validé ET qu'il n'est pas vide

	//debug( $_POST );

	foreach( $_POST as $key => $value ){ //Ici, je passe toutes les informations postées dans les fonctions hmlentities() et addslashes()

		$_POST[$key] = htmlentities( addslashes( $value ) );
	}

	//---------------------------------------------
	//GESTION DE LA PHOTO :
	//debug( $_FILES );
	//debug( $_SERVER );

	if( isset( $_GET['action']) && $_GET['action'] == 'modification' ){ //SI je suis dans le cadre d'une modification, je récupère le chemin en bdd (grâce à l'input type="hidden") que je stocke dans LA variable $photo_bdd !

		$photo_bdd = $_POST['photo_actuelle'];
	}
	//---------------------------------------------

	if( !empty( $_FILES['photo']['name'] ) ){ //SI le nom de la photo (dans $_FILES) n'est pas vide, c'est que l'on a uploader un fichier !

		//Ici, je renomme la photo :
		$nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];
			debug( $nom_photo );

		//Chemin pour accéder à la photo (à insérer en BDD) :
		$photo_bdd = URL . "photo/$nom_photo";
			debug( $photo_bdd );

		//Ou est-ce que l'on souhaite enregistrer notre fichier physique de la photo
		$photo_dossier = $_SERVER['DOCUMENT_ROOT'] . "/boutique/photo/$nom_photo";
		//$_SERVER['DOCUMENT_ROOT'] <=> C:/xampp/htdocs
			debug( $photo_dossier );

		//Enregsitrement de la photo au bon endroit, ici dans le dossier 'photo'
		copy( $_FILES['photo']['tmp_name'], $photo_dossier );
		//copy( arg1, arg2 )
			//arg1 : chemin du fichier source
			//arg2 : chemin de destination
	}
	else{

		//$photo_bdd =''; //Si pas de message d'erreur, on insèrera du 'vide'
		$error .= '<div class="alert alert-danger">PAS DE FICHIER UPLOADER</div>';
	}

	//---------------------------------------------
	//INSERTION  ou MODIFICATION d'un produit :
	if( isset($_GET['action']) && $_GET['action'] == 'modification' ){ //S'il il y a une 'action' dans l'URL ET que cette action est égale à 'modification', alors on effectue une requête de modification :

		execute_requete(" UPDATE produit SET 	reference = '$_POST[reference]',
												categorie = '$_POST[categorie]',
												titre = '$_POST[titre]',
												description = '$_POST[description]',
												couleur = '$_POST[couleur]',
												taille = '$_POST[taille]',
												sexe = '$_POST[sexe]',
												photo = '$photo_bdd',
												prix = '$_POST[prix]',
												stock = '$_POST[stock]'
							WHERE id_produit = '$_GET[id_produit]'
					 ");

		//redirection vers l'affichage :
		header('location:?action=affichage');

	}
	elseif( empty( $error) ) { //SI la variable $error est vide, je fais mon insertion:

		execute_requete(" INSERT INTO produit( reference, categorie, titre, description, couleur, taille, sexe, photo, prix , stock ) 

						VALUES(
								'$_POST[reference]',
								'$_POST[categorie]',
								'$_POST[titre]',
								'$_POST[description]',
								'$_POST[couleur]',
								'$_POST[taille]',
								'$_POST[sexe]',
								'$photo_bdd',
								'$_POST[prix]',
								'$_POST[stock]'
							)
						");
		//redirection vers l'affichage :

		header('location:?action=affichage');

	}
}

//---------------------------------------------
//Affichage des produits :
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des produits ;

	//Je récupère les produits en bdd:
	$r = execute_requete(" SELECT * FROM produit ");

	$content .= '<h2>Liste des produits</h2>';
	$content .= '<p>Nombre de produits dans la boutique : '. $r->rowCount() .'</p>';

	$content .= '<table border="2" cellpadding="5" >';
		$content .= '<tr>';
			for( $i = 0; $i < $r->columnCount(); $i++ ){

				$colonne = $r->getColumnMeta( $i );
					//debug($colonne);
				$content .= "<th>$colonne[name]</th>";
			}
			$content .= '<th>Suppression</th>';
			$content .= '<th>Modification</th>';
		$content .= '</tr>';

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			$content .= '<tr>';
				//debug( $ligne );

				//EXERCICE : affichez les informations ET la photo !
				foreach( $ligne as $indice => $valeur ){

					if( $indice == 'photo' ){ //SI l'index du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>

						$content .= "<td><img src='$valeur' width='80'></td>";
					}
					else{ //SINON, on affiche la valeur dans une cellule simple

						$content .= "<td> $valeur </td>";
					}
				}
				$content .= '<td class="text-center">
								<a href="?action=suppression&id_produit='. $ligne['id_produit'] .'" onclick="return( confirm(\'En etes vous certain ?\') )">
									<i class="far fa-trash-alt"></i>
								</a>	
							</td>';
				$content .= '<td class="text-center">
								<a href="?action=modification&id_produit='. $ligne['id_produit'] .'">
									<i class="far fa-edit"></i>
								</a>	
							</td>';
			$content .= '</tr>';
		}
	$content .= '</table>';
}

//----------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------
?>
<h1>GESTION BOUTIQUE </h1>

<!-- 2 liens pour gérer soit l'affichage soit le formulaire d'ajout selon l'action passée dans l'URL -->
<a href="?action=ajout">Ajout produit</a><br>
<a href="?action=affichage">Affichage des produits</a><hr>

<?= $error; //affichage des erreurs ?>
<?= $content; //affichage du contenu ?>

<?php if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')  ) : //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'ajout' OU à 'modification', alors on affiche le formulaire 

	if( isset( $_GET['id_produit']) ){ //S'il existe 'id_produit' dans l'URL, c'est que je suis dans le cadre d'une modification

		//récupération des infos à modifier :
		$r = execute_requete(" SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]' ");
		//exploitation des données :
		$article_actuel = $r->fetch( PDO::FETCH_ASSOC );
			debug( $article_actuel );
	}

	//condition pour vérifier l'existance des variables :
	if( isset( $article_actuel['reference']) ){

		$reference = $article_actuel['reference']; //on stocke la valeur dans une variable
	}
	else{ //Sinon, on crée cette variable à vide.

		$reference = '';
	}

	//version ternaire des conditions (même chose que la condition du dessus)
	$categorie = ( isset($article_actuel['categorie']) ) ? $article_actuel['categorie'] : '';
	$titre = ( isset($article_actuel['titre']) ) ? $article_actuel['titre'] : '';
	$description = ( isset($article_actuel['description']) ) ? $article_actuel['description'] : '';
	$couleur = ( isset($article_actuel['couleur']) ) ? $article_actuel['couleur'] : '';
	$prix = ( isset($article_actuel['prix']) ) ? $article_actuel['prix'] : '';
	$stock = ( isset($article_actuel['stock']) ) ? $article_actuel['stock'] : '';

	//taille :
	if( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'S' ){ //Si la taille de $article_actuel existe (c'est que l'on est dans une modification) ET QUE cette taille est égale à S

		$taille_s = 'selected'; //on stocke "selected" dans une variable
	}else{

		$taille_s = ''; //SINON, on sotcke du vide dans la variable
	}

	$taille_m = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'M' ) ? 'selected': '';
	$taille_l = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'L' ) ? 'selected': '';
	$taille_xl = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'XL' ) ? 'selected': '';

	//Sexe :
	if( isset( $article_actuel['sexe']) && $article_actuel['sexe'] == 'f' ){ //modif et la valeur = 'f'

		$sexe_f = 'checked';
	}
	else{ //ajout ou que la valeur c'est 'm'
		$sexe_f = '';
	}

	$sexe_m = ( isset( $article_actuel['sexe']) && $article_actuel['sexe'] == 'm' ) ? 'checked' : '';

?>

<form method="post" enctype="multipart/form-data">
	<!-- enctype="multipart/form-data" : cet attribut est OBLIGATOIRE lorsque l'on veut uploader des fichiers et le récupérer via $_FILES -->
	<label>Référence</label><br>
	<input type="text" name="reference" class="form-control" value="<?= $reference ?>"><br>

	<label>Catégorie</label><br>
	<input type="text" name="categorie" class="form-control" value="<?= $categorie ?>"><br>

	<label>Titre</label><br>
	<input type="text" name="titre" class="form-control" value="<?= $titre ?>"><br>

	<label>Description</label><br>
	<input type="text" name="description" class="form-control" value="<?= $description ?>"><br>

	<label>Couleur</label><br>
	<input type="text" name="couleur" class="form-control" value="<?= $couleur ?>"><br>

	<label>Taille</label><br>
	<select name="taille" class='form-control'>
		<option value="S" <?= $taille_s ?> > S </option>
		<option value="M" <?= $taille_m ?> > M </option>
		<option value="L" <?= $taille_l ?> > L </option>
		<option value="XL" <?= $taille_xl ?> > XL </option>
	</select><br><br>

	<label>Civilite</label><br>
	<input type="radio" name="sexe" value="m" <?= $sexe_m ?> > Homme <br>
	<input type="radio" name="sexe" value="f" <?= $sexe_f ?> > Femme <br><br>

	<label>Photo</label><br>
	<input type="file" name="photo"><br><br>
	<?php

		if( isset( $article_actuel['photo']) ){ //S'il existe $article_actuel['photo'] : c'est que je suis dans le cadre d'une modification

			echo "<i>Vous pouvez uploader une nouvelle photo</i>";

			echo "<img src='$article_actuel[photo]' width='80' ><br><br>";

			echo "<input type='hidden' class='form-control' name='photo_actuelle' value='$article_actuel[photo]' >";
		}

	?>		
	<label>Prix</label><br>
	<input type="text" name="prix" class="form-control" value="<?= $prix ?>"><br>

	<label>Stock</label><br>
	<input type="text" name="stock" class="form-control" value="<?= $stock ?>"><br>

	<input type="submit" value="<?= ucfirst($_GET['action']) ?>" class="btn btn-secondary">
	<!-- ucfirst() : permet de mettre la première lettre en masjucule -->
</form>

<?php endif; ?>

<?php require_once '../inc/footer.inc.php'; ?>