<!-- CONFIGURATION DE LA PAGE -->

<?php
# Inclusion du fichier d'intiation
require_once 'inc/init_config.inc.php';

# Inclusion du fichier de navigation
include_once 'inc/header.inc.php';

# Utilisateur non connecté alors redirection vers la page de connexion
if(!isset($_SESSION['utilisateur'])) {
	header('location:connexion.php');
	exit();
}

# Déclaration de variables pour éviter les messages d'erreur à l'ouverture du fichier :
$pseudo = (!empty($_POST['pseudo'])) ? trim(strip_tags($_POST['pseudo'])) : '';
$mdp = (!empty($_POST['mdp'])) ? trim(strip_tags($_POST['mdp'])) : '';
$nom = (!empty($_POST['nom'])) ? trim(strip_tags(ucfirst($_POST['nom']))) : '';
$prenom = (!empty($_POST['prenom'])) ? trim(strip_tags(ucfirst($_POST['prenom']))) : '';
$mail = (!empty($_POST['email'])) ? trim(strip_tags($_POST['email'])) : '';
$sexe = (!empty($_POST['sexe'])) ? trim(strip_tags($_POST['sexe'])) : '';
$ville = (!empty($_POST['ville'])) ? trim(strip_tags(ucfirst($_POST['ville']))) : '';
$cp = (!empty($_POST['cp'])) ? trim(strip_tags($_POST['cp'])) : '';
$adresse = (!empty($_POST['adresse'])) ? trim(strip_tags(ucfirst($_POST['adresse']))) : '';

# Click sur le bouton 'enregistrement' ?
if (isset($_POST['enregistrement'])) {

	if (!empty($mdp) && strlen($mdp) > 3) {

		// Hash du mot de pass:
		$mdp=password_hash($mdp, PASSWORD_DEFAULT);

		$remplace_mdp= $modelisation->prepare("UPDATE membre SET mdp= :mdp WHERE id_membre=:id_membre");

		$remplace_mdp->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
		$remplace_mdp->bindValue(':mdp', $mdp, PDO::PARAM_STR);

		$remplace_mdp->execute();

	}

	if (!empty($pseudo) && strlen($pseudo) > 3) {
		$remplace_membre= $modelisation->prepare("UPDATE membre SET pseudo = :pseudo WHERE id_membre=:id_membre");

		$remplace_pseudo->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
		$remplace_pseudo->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);

		$remplace_pseudo->execute();

		$_SESSION['utilisateur']['pseudo']=$pseudo;

	}

	if (!empty($mail)) {

		// Existence de l'email en BDD ?
		$check_email=$modelisation->prepare("SELECT email FROM membre WHERE email= :email");
		$check_email->bindValue(':email', $mail, PDO::PARAM_STR);
		$check_email->execute();

		if ($check_email->rowCount() < 0) {
			$remplace_email= $modelisation->prepare("UPDATE membre SET email = :email WHERE id_membre=:id_membre");

			$remplace_email->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
			$remplace_email->bindValue(':email', $mail, PDO::PARAM_STR);

			$remplace_email->execute();

			$_SESSION['utilisateur']['email']=$mail;

		}else{

			echo 'Oups ! Cet email est déjà existant ! Vous pouvez réessayez.';
		}
	}

	// Tous les champs du formulaire ont été remplis ?
	if (!empty($nom) || !empty($prenom) || !empty($sexe) || !empty($ville) || !empty($cp) || !empty($adresse)) {
		

		// Remplacement des informations du membre en BDD
		$remplace_membre= $modelisation->prepare("UPDATE membre SET nom= :nom, prenom= :prenom, sexe= :sexe, ville= :ville, cp= :cp, adresse= :adresse WHERE id_membre=:id_membre");

		$remplace_membre->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
		$remplace_membre->bindValue(':cp', $cp, PDO::PARAM_INT);
		$remplace_membre->bindValue(':nom', $nom, PDO::PARAM_STR);
		$remplace_membre->bindValue(':prenom', $prenom, PDO::PARAM_STR);
		$remplace_membre->bindValue(':sexe', $sexe, PDO::PARAM_STR);
		$remplace_membre->bindValue(':ville', $ville, PDO::PARAM_STR);
		$remplace_membre->bindValue(':adresse', $adresse, PDO::PARAM_STR);

		$remplace_membre->execute();

		echo 'Les modifications de votre profil ont bien été prises en compte.';

		$_SESSION['utilisateur']['prenom']=$prenom;
		$_SESSION['utilisateur']['nom']=$nom;
		$_SESSION['utilisateur']['ville']=$ville;
		$_SESSION['utilisateur']['cp']=$cp;
		$_SESSION['utilisateur']['adresse']=$adresse;
		$_SESSION['utilisateur']['sexe']=$sexe;

	} else{
	 		echo 'Attention ! Vous devez remplir tous les champs du formulaire.';
	}
}

?>

<!-- AFFICHAGE -->

<h1>Profil</h1>

<p>Bonjour <?= ($_SESSION['utilisateur']['sexe'] == 'm') ? 'Mr' : 'Mme' ?> <?= ucfirst($_SESSION['utilisateur']['prenom']) ?> <?= strtoupper($_SESSION['utilisateur']['nom']) ?>.</p>

<em>Voici vos informations :</em>

<ul>
	<!-- Affichage des informations de l'utilisateur sauf son nom, prénom, mot de pass, identifiant, sexe et statut -->
	<?php foreach ($_SESSION['utilisateur'] as $key => $value) {
		if ($key !== 'nom' && $key !== 'prenom' && $key !== 'mdp' && $key !== 'id_membre' && $key !== 'sexe' && $key !== 'statut') { ?>
			<ul>
				<li><?= $key.' : '.$value?></li>
			</ul>
		<?php }

	} ?>
</ul>

<a href="?action=up_profil">Mettre à jour mes informations</a>

<!-- Click sur le lien pour mettre à jour les informations de profil ? -->
<?php if (!empty($_GET['action']) && $_GET['action']=='up_profil') : ?>

<em style="display: block;">Veuillez remplir tous les champs du formulaire pour valider la modification de votre profil</em>

<form  method="post" action="">
	<fieldset style="width:10%; margin:10px; padding:10px;">
		<legend>Modifier mon profil</legend>

		<label >Pseudo</label>
		<input style="display:block; margin-left:2px; margin-top:5px;"  placeholder="Minimum trois caractères" type="text" name="pseudo" value="<?= !empty($_SESSION['utilisateur']['pseudo']) ? $_SESSION['utilisateur']['pseudo'] : '' ?>" required>

		<label>Mot de pass</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" placeholder="Minimum trois caractères" type="password" name="mdp" required>

		<label>Nom</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="nom" value="<?= !empty($_SESSION['utilisateur']['nom']) ? $_SESSION['utilisateur']['nom'] : '' ?>" required>

		<label>Prénom</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="prenom" value="<?= !empty($_SESSION['utilisateur']['prenom']) ? $_SESSION['utilisateur']['prenom'] : '' ?>" required>

		<label>Email</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" type="email" name="email" value="<?= !empty($_SESSION['utilisateur']['email']) ? $_SESSION['utilisateur']['email'] : '' ?>" required>

		<label>Sexe</label>
		<select  style="display:block; margin-left:2px; margin-top:5px;" name="sexe"  required>
			<option value="m">M</option>
			<option value="f">F</option>
		</select>

		<label>Ville</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="ville" value="<?= !empty($_SESSION['utilisateur']['ville']) ? $_SESSION['utilisateur']['ville'] : '' ?>" required>

		<label>Code postal</label>
		<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="cp" value="<?= !empty($_SESSION['utilisateur']['cp']) ? $_SESSION['utilisateur']['cp'] : '' ?>" required>

		<label>Adresse</label>
		<textarea  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="adresse" required><?= !empty($_SESSION['utilisateur']['adresse']) ? $_SESSION['utilisateur']['adresse'] : '' ?></textarea>

		<input style="display:block; margin-left:2px; margin-top:10px;" type="submit" value="Modifier" name="enregistrement">
			
	</fieldset>

</form>

<?php
endif;

# Inclusion du fichier de bas de page
include_once 'inc/footer.inc.php';

