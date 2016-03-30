<?php
// Insertion du fichier de configuration

require_once('inc/init_config.inc.php');

// Déclaration de variable pour éviter les messages d'erreur du navigateur

$newsMembre=(!empty($_SESSION['utilisateur']['id_membre']) && is_numeric($_SESSION['utilisateur']['id_membre'])) ? trim(strip_tags($_SESSION['utilisateur']['id_membre'])) : '';

// Conditions pour l'inscription à la newsletter

if (!empty($_GET['action']) && $_GET['action']=='validez' && !empty($_GET['newsletter']) && $_GET['newsletter']=='ok') {

	# Existence du membre en BDD ?
	$check_news_membre= $modelisation->prepare("SELECT id_membre FROM newsletter WHERE id_membre= :id_membre");
	$check_news_membre->bindValue(':id_membre', $newsMembre, PDO::PARAM_INT);
	$check_news_membre->execute();

	# Si le membre n'est pas présent en base de donnée : 
	if ($check_news_membre->rowCount() == 0) {

		$newsMember= $modelisation->prepare("INSERT INTO newsletter(id_membre) VALUES(:id_membre)");
		$newsMember->bindValue(':id_membre', $newsMembre, PDO::PARAM_INT);
		$newsMember->execute();

		echo 'Nous avons bien pris en compte votre inscription à la newsletter';
	
	# Si le membre est déjà inscrit à la newsletter :	
	} else{
		echo 'Désolé ! Vous avez déjà été inscrit pour recevoir nos précieuses actualités.';
	}

# Si le membre ne souhaite pas recevoir d'informations concernant le site
} elseif (!empty($_GET['action']) && $_GET['action']=='validez' && !empty($_GET['newsletter']) && $_GET['newsletter']=='nok') {

	header('location:index.php');
	exit();
}

?>


<!-- Affichage du formulaire pour l'inscription à la newsletter du site -->

<main>
	<p>>> S'inscrire à la newsletter</p>

	<em>Voulez-vous vous inscrire à notre newsletter pour resté informer sur nos nouveautés ?</em>
	<form action="" method="get">
		<label for="valid">Oui</label>
		<input type="radio" id="valid" name="newsletter" value="ok">
		<label for="unvalid">Non</label>
		<input id="unvalid" type="radio" name="newsletter" value="nok">
		<input type="submit" name="action" value="validez">
	</form>	
</main>


<!-- Inclusion du fichier de pied de page -->

<?php
require_once('inc/footer.inc.php');
