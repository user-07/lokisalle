<?php
// Inclusion du fichier de configuration
require_once 'inc/init_config.inc.php';

// Inclusion du fichier d'en-tête de page
require_once 'inc/header.inc.php';

// Déclaration de variables pour éviter les messages d'erreur des navigateurs
$membre_comment=(!empty($_POST['membre_comment'])) ? strip_tags(trim($_POST['membre_comment'])) : '';
$note=(!empty($_POST['number'])) ? strip_tags(trim($_POST['number'])) : '';
$avis_salle=(!empty($avis_produit[0]['id_salle'])) ? $avis_produit[0]['id_salle']: '';

// Conditions pour afficher les informations relatives à la salle sélectionnée par l'utilisateur
if (!empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])) {

	# Récupération des données en base de donnée pour l'affichage des détails du produit sélectionné:
	$recup_details_produit=$modelisation->prepare("SELECT p.id_produit, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart, s.id_salle, s.titre, s.capacite, s.categorie, s.pays, s.ville, s.adresse, s.cp, s.description, s.photo FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle WHERE p.id_produit = :id_produit");

	$recup_details_produit->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
	$recup_details_produit->execute();

	$details_produit=$recup_details_produit->fetchAll(PDO::FETCH_ASSOC);
	#debug($details_produit);

	# Mise en forme de l'adresse du produit pour l'intégrer dans la balise iFrame de GoogleMaps:
 	$adaptAdresse = str_replace(' ', '+', $details_produit[0]['adresse']);

 	# Récupération des données en base de donnée pour l'affichage des avis du produit:
 	$recup_avis_produit=$modelisation->prepare("SELECT s.id_salle, a.commentaire, a.note, DATE_FORMAT(a.date, '%d/%m/%Y à %H:%i') AS 'date', m.prenom FROM salle s INNER JOIN avis a ON s.id_salle=a.id_salle INNER JOIN membre m ON a.id_membre=m.id_membre WHERE s.id_salle = :id_salle");

 	$recup_avis_produit->bindValue(':id_salle', $details_produit[0]['id_salle'], PDO::PARAM_INT);
 	$recup_avis_produit->execute();
 	$avis_produit=$recup_avis_produit->fetchAll(PDO::FETCH_ASSOC);
 	#debug($avis_produit);

	# Calcul de la moyenne des notes données par les membres sur un produit:
	$nbre_commentaires_produit=count($avis_produit);
	$notes_addition=0;

	for ($i= 0; $i < $nbre_commentaires_produit; $i++) { 
		$notes_addition+=$avis_produit[$i]['note'];
	}
	$note_moyenne= (!empty($avis_produit)) ?round(($notes_addition/$nbre_commentaires_produit),1):'';

	# Récupération des données en base de donnée pour l'affichage des suggestions de produit:
	$recup_suggestions_produit= $modelisation->prepare("SELECT p.id_produit, s.id_salle, s.titre, s.photo, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y à %H:%i') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y à %H:%i') AS date_depart, s.capacite, s.ville FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle WHERE etat=0 AND ville= :ville AND id_produit!= :id_produit");

	$recup_suggestions_produit->bindValue(':ville', $details_produit[0]['ville'], PDO::PARAM_STR);
	$recup_suggestions_produit->bindValue(':id_produit', $details_produit[0]['id_produit'], PDO::PARAM_INT);

	$recup_suggestions_produit->execute();
	$nbre_suggestions=$recup_suggestions_produit->rowCount();
	#debug($nbre_suggestions);

	$suggestions_produit= $recup_suggestions_produit->fetchAll(PDO::FETCH_ASSOC);
	#debug($suggestions_produit);
	
}

// Conditions pour enregistrer un avis de produit par un membre
if (isset($_SESSION['utilisateur']) && isset($_POST['noteValid'])) {

	if (!empty($membre_comment) && !empty($note) && is_numeric($note)) {
		$recup_comment_produit=$modelisation->prepare("INSERT INTO avis(id_membre, id_salle, commentaire, note) VALUES(:id_membre, :id_salle, :commentaire, :note)");
		#debug($details_produit[0]['id_salle']);
		$recup_comment_produit->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
		$recup_comment_produit->bindValue(':id_salle', $details_produit[0]['id_salle'], PDO::PARAM_INT);
		$recup_comment_produit->bindValue(':note', $note, PDO::PARAM_INT);
		$recup_comment_produit->bindValue(':commentaire', $membre_comment, PDO::PARAM_STR);

		$recup_comment_produit->execute();
		#debug($recup_comment_produit);
	}
}


?>


<!-- Affichage de la page WEB -->

<main>
	<section id="enTete">
		<bold><?= $details_produit[0]['titre'] ?></bold> <em>(<?= $note_moyenne ?>/10 moyenne sur <?= $nbre_commentaires_produit ?> avis)</em><br>
		<p><img src="<?= URL.'photos/'.$details_produit[0]['photo'] .'" '.'alt="photo de'.$details_produit[0]['titre'] ?>"> / <?= $details_produit[0]['description'] ?></p>
		<?= $details_produit[0]['capacite'].' personnes maxi / Catégorie : '.$details_produit[0]['id_produit'] ?>
	</section>

	<section id="colonneGauche">

		<h4>Informations complémentaires :</h4>
		<p>Pays : <?= $details_produit[0]['pays'] ?></p>
		<p>Ville : <?= $details_produit[0]['ville'] ?></p>
		<p>Adresse : <?= $details_produit[0]['adresse'] ?></p>
		<p>Code postal : <?= $details_produit[0]['cp'] ?></p>
		<p>Date d'arrivée : <?= $details_produit[0]['date_arrivee'] ?> à 10:00</p>
		<p>Date de départ : <?= $details_produit[0]['date_depart'] ?> à 18:00</p>
		<p>Prix<span style="color:red;">*</span> : <?= $details_produit[0]['prix'] ?>€</p>
		<p><span style="color:red;">*</span>Ce prix est hors taxes</p>
		<p>Accès :</p>
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2628.982157868697!2d2.28949231553531!3d48.78223087928007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e670b671138011%3A0xbb4142371109af6e!2s$<?= $adaptAdresse ?>%2C+<?= $details_produit[0]['cp'] ?>+<?= $details_produit[0]['ville'] ?>!5e0!3m2!1sfr!2sfr!4v1457795149188" width="400" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>

		<?php if(isset($_SESSION['utilisateur'])) : ?>
		<form method="post" action="panier.php">
				<input type="text" name="id_produit" value="<?php echo $details_produit[0]['id_produit'] ?>">
				<button type="submit" class="btn btn-primary" name="ajout_panier" >Ajouter au panier </button>
			</form>
		<?php else: ?>
		<a href="<?= URL?>inscription.php">>Inscrivez-vous pour réserver un produit</a>
		<?php endif; ?>

	</section>

	<section id="colonneDroite">

		<h4>Avis :</h4>

		<?php for ($i=0; $i < $nbre_commentaires_produit ; $i++) : ?>
		<article>
			<p style="color:blue;"><?= $avis_produit[$i]['prenom'].', '.$avis_produit[$i]['date'] ?> Note : <?= $avis_produit[$i]['note']?>/10</p>
			<em><?=$avis_produit[$i]['commentaire'] ?></em>
		</article>
		<?php endfor; ?>

		<?php
		if (!empty($_SESSION['utilisateur'])) : 

			# Existence d'un commentaire du membre pour ce produit en BDD ?
			$check_idMembre=$modelisation->prepare("SELECT id_membre FROM avis WHERE id_membre= :id_membre AND id_salle=:id_salle");
			$check_idMembre->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
			$check_idMembre->bindValue(':id_salle', $avis_salle, PDO::PARAM_INT);

			$check_idMembre->execute();

			if ($check_idMembre->rowCount() == 0) :
		?>

		<form action="" method="post">

			<label for="com">Ajouter un commentaire :</label>
			<textarea name="membre_comment" id="com" cols="30" rows="10"></textarea>

			<label for="note">Note(/10) :</label>
			<select name="number">

			<?php for($i=0; $i<=10; $i++) :?>
				<option value="<?= $i ?>"><?= $i ?></option>
			<?php endfor; ?>

			</select>

			<input type="submit" name="noteValid">

		</form>

		<?php 	
			else:
				echo 'Merci pour votre contribution';
			endif;
		else: 
			echo 'Il faut être connecté pour laisser un commentaire';
		endif;
		?>		

	</section>

	<section id="suggestions">

		<h4>Autres suggestions :</h4>
		<?php if($nbre_suggestions>0) : ?>
		<?php for ($i=0; $i < $nbre_suggestions; $i++) : ?>
		<article>

			<img src="<?= URL.'photos/'.$suggestions_produit[$i]['photo']?>" alt="Photo de la<?= $suggestions_produit[$i]['titre'] ?>">
			<p> 
			Du <?= $suggestions_produit[$i]['date_arrivee']?> jusqu'à <?= $suggestions_produit[$i]['date_depart']?> - <?= $suggestions_produit[$i]['ville']?> <?= $suggestions_produit[$i]['prix']?> euros * pour <?= $suggestions_produit[$i]['capacite']?> personnes
			</p> 
			<a href="<?= URL?>reservation_details.php?id_produit=<?= $suggestions_produit[$i]['id_produit']?>">>Voir la fiche détaillée de la <?= $suggestions_produit[$i]['titre']?></a>

		</article>
		<?php endfor; ?>
		<?php else: 
				echo 'Désolé, nous n\'avons pas d\'autres produits du même genre';
			endif;
		?>

	</section>
	
</main>


<!-- Inclusion du fichier de pied de page -->

<?php
require_once 'inc/footer.inc.php';