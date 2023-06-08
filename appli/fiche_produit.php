<?php require_once 'inc/header.inc.php'; ?>
<?php

//Gestion de la SUPPRESSION :
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'suppression'

	//récupération de la colonne 'photo' dans le table 'produit' a condition que 'lid_produit correponde à l'id passée dans l'URL
	$r = execute_requete(" SELECT photo FROM produits WHERE id = '$_GET[id]' ");

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
						dans : $photo_a_supprimer['photo'] (= http://localhost/appli/nom_photo.png : c'est le chemin de la photo en BDD)
		*/
	if( !empty( $chemin_photo_a_supprimer ) && file_exists( $chemin_photo_a_supprimer ) ){

		unlink( $chemin_photo_a_supprimer );
		//unlink( $url_fichier_a_supprimer ) : permet de supprimer un fichier
	}

	//Suppression dans la table 'asset' A CONDITION que l'id produit corresponde à l'id que l'on récupère dans l'URL
	execute_requete(" DELETE FROM produits WHERE id = '$_GET[id_]' ");

}


//Gestion des produits (INSERTION et MODIFICATION) :
if( !empty( $_POST ) ){ //SI le formulaire a été validé ET qu'il n'est pas vide

	//debug( $_POST );

	foreach( $_POST as $key => $value ){ //Ici, je passe toutes les informations postées dans les fonctions hmlentities() et addslashes()

		$_POST[$key] = htmlentities( addslashes( $value ) );
	}
}


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
    $photo_dossier = $_SERVER['DOCUMENT_ROOT'] . "/appli/photo/$nom_photo";
    //$_SERVER['DOCUMENT_ROOT'] <=> C:/xampp/htdocs
        debug( $photo_dossier );

    //Enregsitrement de la photo au bon endroit, ici dans le dossier 'photo'
    copy( $_FILES['photo']['tmp_name'], $photo_dossier );
    //copy( arg1, arg2 )
        //arg1 : chemin du fichier source
        //arg2 : chemin de destination
}
else{

    $photo_bdd =''; //Si pas de message d'erreur, on insèrera du 'vide'
    $error .= '<div class="alert alert-danger">PAS DE FICHIER UPLOADER</div>';
}



	//---------------------------------------------
	//INSERTION  ou MODIFICATION d'un produit :
	if( isset($_GET['action']) && $_GET['action'] == 'modification' ){ //S'il il y a une 'action' dans l'URL ET que cette action est égale à 'modification', alors on effectue une requête de modification :

		execute_requete(" UPDATE asset SET 		libele = '$_POST[libele]',
												categorie = '$_POST[categorie]',
												num_serie = '$_POST[num_serie]',
												num_inv = '$_POST[num_inv]',
												photo = '$photo_bdd',
												prix = '$_POST[prix]',
												stock = '$_POST[stock]',
												date_mise_en_service = '$_post[date_mise_en_service]',
												desc_produit = '$post[desc_produit]',
												date_fin_garantie = '$post[date_fin_garantie]',
												etat_equipement = '$post[etat_equipement]',
												matricule = '$post[matricule]',

							WHERE id_asset = '$_GET[id_asset]'
					 ");

		//redirection vers l'affichage :
		header('location:?action=affichage');

	}
	elseif( empty( $error) ) { //SI la variable $error est vide, je fais mon insertion:

		execute_requete(" INSERT INTO asset( libele, categorie, num_serie, num_inv, photo, prix, sotck, date_mise_en_service, desc_produit , date_fin_garantie, etat_equipement, matricule ) 

						VALUES(
												libele = '$_POST[libele]',
												categorie = '$_POST[categorie]',
												num_serie = '$_POST[num_serie]',
												num_inv = '$_POST[num_inv]',
												photo = '$photo_bdd',
												prix = '$_POST[prix]',
												stock = '$_POST[stock]',
												date_mise_en_service = '$_post[date_mise_en_service]',
												desc_produit = '$post[desc_produit]',
												date_fin_garantie = '$post[date_fin_garantie]',
												etat_equipement = '$post[etat_equipement]',
												matricule = '$post[matricule]',
							)
						");
		//redirection vers l'affichage :

		header('location:?action=affichage');

	}






	//---------------------------------------------
//Affichage des produits :
if( isset($_GET['action']) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mons URL ET que cette 'action' est égale à 'affichage', alors on affiche la liste des produits ;

	//Je récupère les produits en bdd:
	$r = execute_requete(" SELECT id, matricule, etat_equipement, num_serie, prix, desc_produit,  date_mise_en_service, date_fin_garantie, commentaire FROM asset ");

	$content .= '<h2>Liste des produits</h2>';
	$content .= '<p>Nombre de produits  : '. $r->rowCount() .'</p>';

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
								<a href="?action=suppression&id_produits='. $ligne['id_produits'] .'" onclick="return( confirm(\'En etes vous certain ?\') )">
									<i class="far fa-trash-alt"></i>
								</a>	
							</td>';
				$content .= '<td class="text-center">
								<a href="?action=modification&id_produits='. $ligne['id_produits'] .'">
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
<h1>PANNEAU DE GESTION DES ASSETS </h1>

<!-- 2 liens pour gérer soit l'affichage soit le formulaire d'ajout selon l'action passée dans l'URL -->
<a href="?action=ajout">Ajout de produits (ASSETS)</a><br>
<a href="?action=affichage">Affichage des produits(ASSETS)</a><hr>

<?= $error; //affichage des erreurs ?>
<?= $content; //affichage du contenu ?>

<?php if( isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')  ) : //S'il existe une 'action' dans l'URL ET que cette 'action' est égale à 'ajout' OU à 'modification', alors on affiche le formulaire 

	if( isset( $_GET['id_asset']) ){ //S'il existe 'id_produit' dans l'URL, c'est que je suis dans le cadre d'une modification

		//récupération des infos à modifier :
		$r = execute_requete(" SELECT * FROM asset WHERE id_asset = '$_GET[id_asset]' ");
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
	$matricule = ( isset($article_actuel['matricule']) ) ? $article_actuel['matricule'] : '';
	$etat = ( isset($article_actuel['etat']) ) ? $article_actuel['etat'] : '';
	





?>

<form method="post" enctype="multipart/form-data">
	<!-- enctype="multipart/form-data" : cet attribut est OBLIGATOIRE lorsque l'on veut uploader des fichiers et le récupérer via $_FILES -->
	<label>Référence</label><br>
	<input type="text" name="reference" class="form-control" value="<?= $reference ?>"><br>

	
	<label>Catégorie</label><br>
	<!-- Formulaire autocompletion-->
		<form autocomplete ="off" action="">
			<input type="text" id="myInput"  placeholder=" Ecrire ici la catégorie désirée" class="form-control" value="<?= $categorie ?>">	
			<ul id="autocomplete-list"></ul>
		</form>

		<label>Matricule</label><br>
	<input type="text" name="matricule" class="form-control" value="<?= $matricule ?>"><br>

	<label>Titre</label><br>
	<input type="text" name="titre" class="form-control" value="<?= $titre ?>"><br>

	<label>Description</label><br>
	<input type="text" name="description" class="form-control" value="<?= $description ?>"><br>


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

	<label>Quantité en stock</label><br>
	<input type="text" name="stock" class="form-control" value="<?= $stock ?>"><br>

	
	<!--<label>Lieu</label><br>
	<input type="text" name="lieu" class="form-control" value="<?= $lieu ?>"><br> -->

	<input type="submit" value="<?= ucfirst($_GET['action']) ?>" class="btn btn-secondary">
	<!-- ucfirst() : permet de mettre la première lettre en masjucule -->

</form>
<br> <br>
		<form action="post" action="inscriptionDesProduits.php">
			<input type="submit" class="btn btn-secondary" name="redirect" value="Cliquez ici si votre produit n'existe pas">
		</form>
		<br>
<!-- SCRIPT -->
		<script src="script.js"></script>


<?php endif; ?>

<?php require_once 'inc/footer.inc.php'; ?>
