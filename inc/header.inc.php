<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Loki Salle</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	</head>

	<body>
		<header>
			<h1><img src=""></h1>

			<nav>
				<ul>
					<li><a href="<?= URL ?>index.php">Accueil</a></li>
					<li><a href="<?= URL ?>reservation.php">Réservation</a></li>
					<li><a href="<?= URL ?>recherche.php">Rechercher</a></li>
					
					<!-- Si l'utilisateur est connecté on affiche Se connecter, sinon on affiche deconnexion-->
					<?php if(!isset($_SESSION['utilisateur'])) {?>
						<li><a href="<?= URL ?>connexion.php">Se connecter</a></li>
					<?php }else{ ?>
						<li><a href="<?= URL ?>connexion.php?action=deconnexion">Deconnexion</a></li>
					<?php } ?>
					
					<?php if(!isset($_SESSION['utilisateur'])) {?>
						<li><a href="<?= URL ?>inscription.php">Créer un nouveau compte</a></li>
					<?php }else{ ?>
						<li><a href="<?= URL ?>profil.php">Mon profil</a></li>
					<?php } ?>

					<?php if (!empty($_SESSION['utilisateur']) && $_SESSION['utilisateur']['statut']==1) :
						require_once 'menu.inc.php';
					endif; ?>
				</ul>
			</nav>
		</header>