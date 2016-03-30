<!-- Inclusion du fichier de configuration -->

<?php require_once 'inc/init_config.inc.php'; ?>


<!-- Affichage du plan du site -->

<main>
	<section>
		<ul>
			<li><a href="<?= URL ?>index.php">Accueil</a></li>
			<li><a href="<?= URL ?>reservation.php">Réservation</a></li>
			<li><a href="<?= URL ?>recherche.php">Rechercher</a></li>
					
		<!-- Si l'utilisateur est connecté : on lui propose de se déconnecter, sinon on lui propose de se connecter -->
		<?php if(!isset($_SESSION['utilisateur'])) {?>
			<li><a href="<?= URL ?>connexion.php">Se connecter</a></li>
		<?php }else{ ?>
			<li><a href="<?= URL ?>connexion.php?action=deconnexion">Deconnexion</a></li>
		<?php } ?>
		
		<!-- Si l'utilisateur est connecté : on lui propose de voir son profil, sinon on lui propose de s'inscrire -->
		<?php if(!isset($_SESSION['utilisateur'])) {?>
			<li><a href="<?= URL ?>inscription.php">Créer un nouveau compte</a></li>
		<?php }else{ ?>
			<li><a href="<?= URL ?>profil.php">Mon profil</a></li>
		<?php } ?>

		</ul>
	</section>
</main>


<!-- Inclusion du fichier de pied de page -->

<?php
require_once('inc/footer.inc.php');