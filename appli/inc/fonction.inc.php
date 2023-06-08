<?php
//fonction de débugage : debug() permet d'effectuer un print "amélioré"
function debug( $arg ){

	echo '<div style="background:#fda500; z-index:1000; padding:15px">';

		$trace = debug_backtrace();
		//debug_backtrace() : fonction interne de php qui retourne un array contenant des infos.
		echo "Debug demandé dans le fichier : " . $trace[0]['file'] . ' à la ligne ' . $trace[0]['line'];

		print '<pre>';
			print_r( $arg );
		print '</pre>';
	echo '</div>';
}

//--------------------------------------------------------
//fonction execute_requete() : permet d'effectuer une requête
function execute_requete( $req ){

	global $pdo;

	$r = $pdo->query( $req );

	return $r;
}

//--------------------------------------------------------
//fonction userConnect() : si l'interntaute est connecté 
function userConnect(){

	if( !isset( $_SESSION['user'] ) ){ //SI la session 'user' N'EXISTE PAS, cela signifie que l'on n'est pas connecté et donc on renvoie false
		//On crée et rempli la session 'user' lors de la connexion !!!

		return false;
	}
	else{ //SINON, c'est que session 'user' existe et donc que l'on est connecté, on retourne true
	
		return true;
	}
}
//--------------------------------------------------------
//fonction adminConnect() :  Si l'internaute est connecté ET qu'il est administrateur
function adminConnect(){

	if( userConnect() && $_SESSION['user']['statut'] == 1 ){ //SI l'internaute est connecté ET qu'il est admin (donc qu'il à un statut égal à 1 )

		return true;
	}
	else{ 

		return false;
	}
}

//--------------------------------------------------------
//fonction pour créer un panier :
function creation_panier(){

	if( !isset( $_SESSION['panier'] ) ){ //SI la session/panier N'EXISTE PAS

		$_SESSION['panier'] = array(); //création d'une session/panier

			$_SESSION['panier']['libele'] = array();
			$_SESSION['panier']['id_produits'] = array();
			$_SESSION['panier']['quantite_demandee'] = array();
			$_SESSION['panier']['prix'] = array();
	}
}

//--------------------------------------------------------
//fonction d'ajout d'un produit dans le panier :
function ajout_panier( $libele, $id_produit, $quantite_demandee, $prix ){

	creation_panier();
	//ici, on fait appel à la fonction déclarée au dessus
		//SOIT le panier n'xiste pas et donc on le crée (LA première fois que l'on tente d'ajouter un produit à notre panier)
		//SOIT il existe et donc on l'utilise

	$index = array_search( $id_produit, $_SESSION['panier']['id_produits'] );
	//array_search( arg1, arg2 );
		//arg1 : ce que l'on cherche
		//arg2 : dans quel tableau on effectue la recherche
	//VALEUR DE RETOUR : la fonction renverra la "clé" (corresopndante à l'indice du tableau SI il y a une correspondance) ou "false"

	if( $index !== false ){ //SI $index est différent de "false", c'est que le produit est déjà rpésent dans le panier

		$_SESSION['panier']['quantite_demandee'][$index] += $quantite_demandee;
		//Ici, on va précisément à l'indice du produit déja présent dans le panier et on y ajoute la nouvelle quantite

	}
	else{ //SINON, c'est que le produit n'est pas dans le panier donc on insert toutes les informations nécessaires

		$_SESSION['panier']['libele'][] = $libele;
		$_SESSION['panier']['id_produits'][] = $id_produits;
		$_SESSION['panier']['quantite_demandee'][] = $quantite_demandee;
		$_SESSION['panier']['prix'][] = $prix;

	}
}

//--------------------------------------------------------
//fonction pour le montant total du panier
function montant_total(){

	$total = 0;

	for( $i = 0; $i < sizeof( $_SESSION['panier']['id_produits'] ); $i++ ){

		$total += $_SESSION['panier']['quantite_demandee'][$i] * $_SESSION['panier']['prix'][$i];
	}

	return $total;
}

//--------------------------------------------------------
//fonction pour retirer un produit du panier :
function retirer_produit_panier( $id_produit_a_supprimer ){

	$index = array_search( $id_produit_a_supprimer, $_SESSION['panier']['id_produits'] );

	if( $index !== false ){ //SI le produit existe

		array_splice( $_SESSION['panier']['libele'], $index, 1 );
		array_splice( $_SESSION['panier']['id_produits'], $index, 1 );
		array_splice( $_SESSION['panier']['quantite_demandee'], $index, 1 );
		array_splice( $_SESSION['panier']['prix'], $index, 1 );
		//array_splice( arg1, arg2, arg3 ) : permet ed supprimer un/des éléments d'un tableau
			//arg1 :le tableau dans lequel on va faire une suppression
			//arg2 : l'élément que l'on cherche à supprimer
			//arg3 : le nombre d'élément que l'on souhaite supprimer (à partir de l'indice (arg2))
	}
}
