<?php
function debug($arg, $mode=1) {
	echo '<div style="display: inline-block; padding:10px; position: relative; z-index: 1000; background:#16a085;">';
		echo '<pre>';
	if($mode==1) {
		print_r($arg);
	} else {
		var_dump($arg);
	}
		echo '</pre>';
	echo '</div>';
}

function convert_date($param1){

	if (preg_match('/\//', $param1)) {
		$date = explode("/", $param1);
		$date_us= $date[2].'-'.$date[1].'-'.$date[0];
		return $date_us;
	}

	else if(preg_match('/-/', $param1)){
		$date = explode("-", $param1);
		$date_us= $date[2].'-'.$date[1].'-'.$date[0];
		return $date_us;
	}
	
}

function checkExtensionPhoto() {
	//debug($_FILES['photo']['name']);
	$extension = strRchr($_FILES['photo']['name'], '.'); // cette fonction trouve le dernier caractère indiqué et donne la chaine de caractère qui reste, à partir de celui-ci
	//debug($extension); // .jpeg, .png
	$extension = strToLower($extension); // passage en minuscule
	$extension = subStr($extension, 1); // tu me donne le jpg sans le point
	$extensions_valides = ['jpg', 'jpeg', 'png', 'gif']; // je créé un tableau qui contient toutes les extensions valides
	//debug($extension); // jpg etc...
	//debug($extensions_valides);
	$verif_extension = in_array($extension, $extensions_valides); // cette fonction trouve ce qu'on lui donne en 1er argument dans ce qu'on lui donne en 2eme argument
	return $verif_extension; // si y'a autre chose que les extensions du tableau, il retournera false, sinon il retournera true
}


function recupInfoProduit($id) {
	global $modelisation; // pour recuperer la variable de l'espace global qui contient la connexion à la BDD
	$infoProduit = $modelisation->prepare("SELECT * FROM produit INNER JOIN salle ON produit.id_salle = salle.id_salle  WHERE id_produit = :id_produit");
	$infoProduit->bindValue(':id_produit', $id, PDO::PARAM_INT);
	$infoProduit->execute();
	if($infoProduit->rowCount() == 1) { // si je trouve un produit je le return
		$resultat = $infoProduit->fetchAll(PDO::FETCH_ASSOC);
		$produit = $resultat[0]; // je recupere le produit
	} else { // sinon, j'envoi false
		$produit = false;
	}
	return $produit;
}

//--- fonctions de panier ------ //
function creationPanier() {
	if(!isset($_SESSION['panier'])) { // si le panier n'existe pas
		$_SESSION['panier'] = array(); // je cré le tableau panier
		$_SESSION['panier']['titre'] = array(); // je créé le tableau titre dans panier
		$_SESSION['panier']['id_produit'] = array();
		$_SESSION['panier']['prix'] = array();
		$_SESSION['panier']['photo'] = array();

	}
}

function ajouterArticleDansPanier($titre, $id_produit,$prix,$photo) {
	$_SESSION['panier']['titre'][] = $titre; // avec les crochets vides c'est comme si j'écrivais $_SESSION['panier']['titre'][0] = $titre, [1] = $titre etc.. Chaque produit qui va s'ajouter dans le Panier, aura automatiquement une clé numérique incrémentée
	$_SESSION['panier']['id_produit'][] = $id_produit;
	$_SESSION['panier']['prix'][] = $prix;
	$_SESSION['panier']['photo'][] = $photo;
}
function retirerArticleDuPanier($id_a_suppr) {
	$position_article = array_search($id_a_suppr, $_SESSION['panier']['id_produit']);
	if($position_article !== false) { // si array_search me renvoi un nombre, c'est qu'il a trouvé quelque chose
	array_splice($_SESSION['panier']['titre'], $position_article, 1); // arry_splice permet de supprimer un élément du tableau et de reorganiser le tableau en recommançant à partir de zéro
	array_splice($_SESSION['panier']['id_produit'], $position_article, 1);
	array_splice($_SESSION['panier']['prix'], $position_article, 1);
	array_splice($_SESSION['panier']['photo'], $position_article, 1);
	}
}
function calculMontantTotal() {
	$nbre_de_produits = count($_SESSION['panier']['id_produit']);
	$resultat = 0;
	for($i=0; $i<$nbre_de_produits; $i++) {
	$resultat += $_SESSION['panier']['prix'][$i];
	}
	return round($resultat,2);
}