<?php
// Configuration de la page WEB :

require_once 'inc/init_config.inc.php';
require_once 'inc/header.inc.php';
//var_dump($modelisation);


	$recup_derniers_produit= $modelisation->query("SELECT p.id_produit, s.id_salle, s.titre, s.photo, p.prix, DATE_FORMAT(p.date_arrivee, '%d/%m/%Y à %H:%i') AS date_arrivee, DATE_FORMAT(p.date_depart, '%d/%m/%Y à %H:%i') AS date_depart, s.capacite, s.ville FROM produit p INNER JOIN salle s ON s.id_salle=p.id_salle AND etat=0 ORDER BY date_arrivee DESC LIMIT 0,3;");
	$info_derniers_produit= $recup_derniers_produit->fetchAll(PDO::FETCH_ASSOC);
	//var_dump($info_derniers_produit);
	//debug($_SESSION['utilisateur']);

// Affichage de la page WEB :

// presentation 
?>

<main>
	<section id="presentation">


		<h2>Lokisalle</h2>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam eget mauris leo. Etiam scelerisque lectus vel justo tristique, a interdum odio feugiat. Vivamus hendrerit non mi sit amet ultrices. </p>
		<p>Maecenas sed ipsum in leo vulputate pharetra sit amet nec turpis. Sed bibendum vel sem ac aliquam. Ut ac porttitor justo. In porttitor egestas elit. Aenean tincidunt fermentum nisi, ut scelerisque quam lacinia non.</p> 

	</section> 

	<section class="actualite">
		<h3>Nos trois dernières offres</h3>

	<?php for ($i= 0; $i < 3; $i++) : ?>

		<article>
		<img src="<?= URL.'photos/'.$info_derniers_produit[$i]['photo']?>" alt="Photo de la<?= $info_derniers_produit[$i]['titre'] ?>">
		<p> 
		Du <?= $info_derniers_produit[$i]['date_arrivee']?> jusqu'à <?= $info_derniers_produit[$i]['date_depart']?> - <?= $info_derniers_produit[$i]['ville']?> <?= $info_derniers_produit[$i]['prix']?> euros * pour <?= $info_derniers_produit[$i]['capacite']?> personnes
		</p> 
		<a href="<?= URL?>reservation_details.php?id_produit=<?= $info_derniers_produit[$i]['id_produit']?>">>Voir la fiche détaillée</a>

	<?php if (empty($_SESSION['utilisateur'])) : ?>
		<a href="<?= URL?>connexion.php">>Connectez-vous pour l'ajouter au panier</a>
	<?php else: ?>
		<form method="post" action="panier.php">
				<input type="text" name="id_produit" value="<?php echo $info_derniers_produit[$i]['id_produit'] ?>">
				<button type="submit" class="btn btn-primary" name="ajout_panier" >Ajouter au panier </button>
	<?php endif; ?>
		</article>

	<?php endfor; ?>

	</section> 
</main>

<?php require_once 'inc/footer.inc.php';