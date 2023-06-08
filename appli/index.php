<?php require_once "inc/header.inc.php"; ?>
<?php
//Affichage des produits :

//Ici, je récuère les différentes catégories de ma table 'produits' :
$r = execute_requete(" SELECT(categorie_id) FROM produits");
$result= $r->fetchAll();
debug( $result);

foreach( $result as $cle){
    $chat=
    execute_requete( "SELECT(nom) FROM categories WHERE id= '".$cle['categorie_id']."'" );
    $result= $chat->fetchall();
   // debug( $result);
};
   // debug( $chat);

$content .= '<div class="row">';

	//affichage des catégories
	$content .=  '<div class="col-3">';
		$content .=  '<div class="list-group-item">';

			while( $info = $chat->fetch( PDO::FETCH_ASSOC ) ){

				//debug( $info );
				$content .= "<a href='?categories=$info[categories]'  class='list-group-item'>
								$info[categories]
							</a>";
			}
		$content .=  '</div>';
	$content .=  '</div>';

	//Affichage des produits correspondants à la catégories sélectionée :
	$content .= '<div class="col-8 offset-1">';
		$content .= '<div class="row">';

//debug( $_GET );
if( isset( $_GET['categories'] ) ){

	//ici, htmlentities permet de gérer les accents des catégories
	$cat = htmlentities( $_GET['categories'] );

	$r = execute_requete(" SELECT * FROM produits WHERE categories = '$cat' ");

	while( $produit = $r->fetch( PDO::FETCH_ASSOC ) ){

		debug( $produit );
		$content .= '<div class="col-4">';
			$content .= '<div class="thumbnail" style="border:1px solid #eee;">';

				$content .= "<a href='fiche_produit.php?id_produit=$produit[id_produits]'>";

					$content .= "<img src='$produit[photo]' width='100'>";
					$content .= "<p>$produit[titre]</p>";
					$content .= "<p>$produit[prix]</p>";

				$content .= '</a>';

			$content .= '</div>';
		$content .= '</div>';
	}
}

		$content .= '</div>';
	$content .= '</div>';
$content .= '</div>';

//-----------------------------------------------------------------------------------------
?>
<h1>ACCUEIL DU CATALOGUE</h1>

<?= $content; //affichage du contenu ?>

<?php require_once "inc/footer.inc.php"; ?>