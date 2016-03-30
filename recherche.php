<?php
// inclure les fichiers de configuration
require_once 'inc/init_config.inc.php';

// inclure les fichiers d'affichage
require_once 'inc/header.inc.php';

// Déclaration de variables pour éviter les messages d'erreur du navigateur :
$date_entree = (!empty($_POST['date_entree'])) ? trim(htmlspecialchars($_POST['date_entree'])) : '';
$cle = (!empty($_POST['cle'])) ? trim(strip_tags($_POST['cle'])) : '';

// Click du bouton 'rechercher' ?
if (isset($_POST['search'])) {

	// Recherche par mot clé ?
	if (!empty($cle)) {
		
		$recup_mot_cle= $modelisation->prepare("SELECT s.id_salle, s.titre, s.photo, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart, s.capacite, s.ville, p.id_produit FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle WHERE  etat=0  AND (ville= :cle OR categorie= :cle OR titre= :cle) ORDER BY date_arrivee DESC");

			$recup_mot_cle->bindValue(':cle', $cle, PDO::PARAM_STR);

			$recup_mot_cle->execute();


			$recherche= $recup_mot_cle->fetchAll(PDO::FETCH_ASSOC);
			// debug($recherche);
			$nbre_recherche= count($recherche);

	}
	// Recherche par date ?
	else if (!empty($date_entree)) {
		// Conversion de la date française en date americaine pour l'interprétation de mySQL
		$date_entree=convert_date($date_entree);

		$recup_date_entree= $modelisation->prepare("SELECT s.id_salle, s.titre, s.photo, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart, s.capacite, s.ville, p.id_produit FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle WHERE etat=0 AND date_arrivee= :date_arrivee ORDER BY date_arrivee DESC");

			$recup_date_entree->bindValue(':date_arrivee', $date_entree, PDO::PARAM_STR);

			$recup_date_entree->execute();

			$recherche= $recup_date_entree->fetchAll(PDO::FETCH_ASSOC);

			$nbre_recherche= $recup_date_entree->rowCount();
	} 
	
}

?>

<main>
	  <h1>Rechercher : </h1>
	  <section class="formulaire">
	    <center>
	      <form action="" method="post">
		       <fieldset style="width:50%; padding:10px;">
					 <legend>Recherche d'une location de salle pour réservation</legend>
	     				<label>A la date du :</label><br><br>
	     				<label>Date d'arrivée : </label><input type="text" name="date_entree" placeholder="jj/mm/aaaa"><br>
	     				<br><hr><br>
	     				<label>Par mots clé : </label><br><br>
	     				<input type="text" name="cle" placeholder="Ex: Paris, nom de salle, type de demande"><br>
	     				<br><hr><br>
	        			<input type="submit" value="recherche" name="search">
		        </fieldset>
	      </form>
	    </center>
	  </section>

<?php if (isset($_POST['search']) && (!empty($cle) || !empty($date_entree))) : ?>
	<h2>Résultats de la recherche : </h2>
	<section class="actualite">
		<p>Résultats de votre recherche</p>

		<em>Nombre de résultat trouvé(s) : <?= $nbre_recherche ?></em>
		<?php for ($i= 0; $i < $nbre_recherche; $i++) : ?>

		<article>
		<img src="<?= URL.'photos/'.$recherche[$i]['photo']?>" alt="Photo de la salle <?= $recherche[$i]['photo'] ?>" width="250px">
		<p> 
		Du <?= $recherche[$i]['date_arrivee']?> jusqu'à <?= $recherche[$i]['date_depart']?> - <?= $recherche[$i]['ville']?> <?= $recherche[$i]['prix']?> euros * pour <?= $recherche[$i]['capacite']?> personnes
		</p> 
		<a href="<?=URL?>reservation_details.php?id_produit=<?= $recherche[$i]['id_produit'] ?>">>Voir la fiche détaillée</a>

		<!-- Si l'utilisateur n'est pas connecté : bouton 'connexion' -->
		<?php if (empty($_SESSION['utilisateur'])) : ?>
			<a href="<?= URL?>connexion.php">>Connectez-vous pour l'ajouter au panier</a>

		<!-- Si l'utilisateur est connecté : bouton 'ajouter au panier' -->
		<?php else : ?>
			<form method="post" action="panier.php">
				<input type="text" name="id_produit" value="<?php echo $recherche[$i]['id_produit'] ?>">
				<button type="submit" class="btn btn-primary" name="ajout_panier" >Ajouter au panier </button>
			</form>
		</article>
		<?php endif; ?>

		<?php endfor; ?>

	</section
<?php endif; ?>
</main>

<?php
require_once 'inc/footer.inc.php';