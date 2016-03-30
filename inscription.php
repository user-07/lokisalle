<?php
	// Appel du fichier de configuration :
	require_once 'inc/init_config.inc.php';

	// Appel du fichier de 'header' (menu de navigation) :
	require_once 'inc/header.inc.php';

	// Déclaration de variables pour éviter les messages d'erreur à l'ouverture du fichier :
	$pseudo = (!empty($_POST['pseudo'])) ? trim(strip_tags($_POST['pseudo'])) : '';
	$mdp = (!empty($_POST['mdp'])) ? trim(strip_tags($_POST['mdp'])) : '';
	$nom = (!empty($_POST['nom'])) ? trim(strip_tags(ucfirst($_POST['nom']))) : '';
	$prenom = (!empty($_POST['prenom'])) ? trim(strip_tags(ucfirst($_POST['prenom']))) : '';
	$mail = (!empty($_POST['email'])) ? trim(strip_tags($_POST['email'])) : '';
	$sexe = (!empty($_POST['sexe'])) ? trim(strip_tags(strtoupper($_POST['sexe']))) : '';
	$ville = (!empty($_POST['ville'])) ? trim(strip_tags(ucfirst($_POST['ville']))) : '';
	$cp = (!empty($_POST['cp'])) ? trim(strip_tags($_POST['cp'])) : '';
	$adresse = (!empty($_POST['adresse'])) ? trim(strip_tags(ucfirst($_POST['adresse']))) : '';

	// Click sur le bouton 'enregistrement' ?
	if (isset($_POST['enregistrement'])) {

		// Tous les champs du formulaire ont été remplis ?
		if (!empty($pseudo) && !empty($mdp) && !empty($nom) && !empty($prenom) && !empty($mail) && !empty($sexe) && !empty($ville) && !empty($cp) && !empty($adresse)) {

			// Pseudo et mot de pass possède au minimum 3 caractères ?
			if ((strlen($pseudo) > 3) && (strlen($mdp) > 3)) {
				
				// Existence de l'email en BDD ?
				$check_email=$modelisation->prepare("SELECT email FROM membre WHERE email= :email");
				$check_email->bindValue(':email', $mail, PDO::PARAM_STR);
				$check_email->execute();

				if ($check_email->rowCount() > 0) {
					$msg= 'Oups ! Cet email est déjà existant ! Vous pouvez réessayez.';
				} else{

					// Hash du mot de pass:
					$mdp=password_hash($mdp, PASSWORD_DEFAULT) ;

					// Insertion des informations du nnouveau membre en BDD
					$insertion_membre= $modelisation->prepare("INSERT INTO membre(pseudo, mdp, nom, prenom, email, sexe, ville, cp, adresse) VALUES(:pseudo, :mdp, :nom, :prenom, :email, :sexe, :ville, :cp, :adresse)");

					$insertion_membre->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
					$insertion_membre->bindValue(':mdp', $mdp, PDO::PARAM_STR);
					$insertion_membre->bindValue(':nom', $nom, PDO::PARAM_STR);
					$insertion_membre->bindValue(':prenom', $prenom, PDO::PARAM_STR);
					$insertion_membre->bindValue(':email', $mail, PDO::PARAM_STR);
					$insertion_membre->bindValue(':sexe', $sexe, PDO::PARAM_STR);
					$insertion_membre->bindValue(':ville', $ville, PDO::PARAM_STR);
					$insertion_membre->bindValue(':adresse', $adresse, PDO::PARAM_STR);
					$insertion_membre->bindValue(':cp', $cp, PDO::PARAM_INT);

					$insertion_membre->execute();

					$msg= 'Bravo ! Votre inscription a bien été prise en compte.';
					header('location:profil.php');
					exit();
				}
			}else{
				$msg='Attention ! Votre pseudo et votre mot de pass doit contenir au minimum trois caractères.';
			}
		 } else{
		 		$msg= 'Attention ! Vous devez remplir tous les champs du formulaire.';
		 }
	}
?>

<!-- Formulaire d'inscription -->

<main>
	<h2>>>Inscription <?= (isset($msg)) ? ' : '.$msg : ' ' ?></h2>

	<form  method="post" action="">
		 <fieldset style="width:10%; margin:10px; padding:10px;">
			 <legend>Inscription</legend>
				<label >Pseudo</label>
				<input style="display:block; margin-left:2px; margin-top:5px;"  placeholder="Minimum trois caractères" type="text" name="pseudo" required>

				<label>Mot de pass</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" placeholder="Minimum trois caractères" type="password" name="mdp" required>

				<label>Nom</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="nom" required>

				<label>Prénom</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="prenom" required>

				<label>Email</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" type="email" name="email" required>

				<label>Sexe</label>
				<select  style="display:block; margin-left:2px; margin-top:5px;" name="sexe" required>
					<option value="m">M</option>
					<option value="f">F</option>
				</select>

				<label>Ville</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="ville" required>

				<label>Code postal</label>
				<input  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="cp" required>

				<label>Adresse</label>
				<textarea  style="display:block; margin-left:2px; margin-top:5px;" type="text" name="adresse" required></textarea>

				<input style="display:block; margin-left:2px; margin-top:10px;" type="submit" value="Inscription" name="enregistrement">
			
		</fieldset>


	</form>
</main>

<!-- Appel du fichier 'footer' (pied de page) -->
<?php
	require_once 'inc/footer.inc.php';
?>