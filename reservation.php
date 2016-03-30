<?php
 require_once 'inc/init_config.inc.php';
 require_once 'inc/header.inc.php';


$all_items = $modelisation->query("SELECT s.titre, s.id_salle, s.capacite, s.ville, s.adresse, s.photo, p.id_produit, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y') AS  date_arrivee, p.date_depart, DATE_FORMAT(p.date_depart, '%d/%m/%Y') AS date_depart FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle AND etat=0 ORDER BY date_arrivee DESC ");
$items_infos = $all_items->fetchAll(PDO::FETCH_ASSOC);
// var_dump($items_infos);

$nbre_salle_dispo = count($items_infos);
// echo $nbre_salle_dispo;

//debug($items_infos);

?>
<main>

	<h1>Réservation</h1>
	<h2 class="centre_bloc">Toute nos offres</h2>
	
	<?php for($i=0; $i<$nbre_salle_dispo;$i++) : ?>
		<div class="divstyle">
			<img src="<?= URL.'photos/'.$items_infos[$i]['photo'] .'" '.'alt="photo de'.$items_infos[$i]['titre'] ?>">
			<p class="flotte"> 
			Du <?=$items_infos[$i]['date_arrivee']?> au <?=$items_infos[$i]['date_depart']?> ‐ <?=$items_infos[$i]['ville']?>  <?=$items_infos[$i]['prix']?> euros * <?=$items_infos[$i]['capacite']?> personnes
			</p>	
			<a class="important" href="<?= URL?>reservation_details.php?id_produit=<?= $items_infos[$i]['id_produit']?>">> Voir la fiche détaillée  </a>

			<?php if (empty($_SESSION['utilisateur'])) : ?>
			<a href="<?= URL?>connexion.php">>Connectez-vous pour l'ajouter au panier</a>
			<?php else: ?>
			<form method="post" action="panier.php">
				<input type="text" name="id_produit" value="<?php echo $items_infos[$i]['id_produit'] ?>">
				<button type="submit" class="btn btn-primary" name="ajout_panier" >Ajouter au panier </button>
			</form>
			<?php endif; ?>
		</div>		
	<?php endfor ?>

</main>

<?php 
 include_once 'inc/footer.inc.php';