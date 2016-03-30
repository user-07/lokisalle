<?php
// Inclusion des fichier d'initialisation et de header :

require_once 'inc/init_config.inc.php';
include_once 'inc/header.inc.php';

// Si l'utilisateur se déconnecte alors les informations de sessions sont vidées :

if(!empty($_GET['action']) && $_GET['action'] == 'deconnexion') {
	unset($_SESSION['utilisateur']); 
}
// Si la connexion a été réalisée alors redirection de l'utilisateur vers sa page profil:

if(!empty($_SESSION['utilisateur'])) {
	header('profil.php');
	exit();
}

// Connexion de l'utilisateur:

if(isset($_POST['connexion'])) {
	$email = (!empty($_POST['email'])) ? trim($_POST['email']) : '';
	$mdp = (!empty($_POST['mdp'])) ? trim(htmlspecialchars($_POST['mdp'])) : '';

	$recup_mdp = $modelisation -> prepare("SELECT id_membre,mdp, pseudo, email, mdp, nom, prenom, sexe, ville, cp, adresse, statut FROM membre WHERE email = :email");
	$recup_mdp->bindValue(':email', $email, PDO::PARAM_STR);
	$recup_mdp->execute();

	if($recup_mdp->rowCount() == 1) { // si je trouve quelqu'un
		$membre = $recup_mdp->fetchAll(PDO::FETCH_ASSOC);
		
		if(password_verify($mdp, $membre[0]['mdp'])) {
			$msg = '<div class="valide">Ok</div>';

				$_SESSION['utilisateur']['id_membre'] = $membre[0]['id_membre'];
				$_SESSION['utilisateur']['pseudo'] = $membre[0]['pseudo'];
				$_SESSION['utilisateur']['email'] = $membre[0]['email'];
				$_SESSION['utilisateur']['mdp'] = $membre[0]['mdp'];
				$_SESSION['utilisateur']['nom'] = $membre[0]['nom'];
				$_SESSION['utilisateur']['prenom'] = $membre[0]['prenom'];
				$_SESSION['utilisateur']['sexe'] = $membre[0]['sexe'];
				$_SESSION['utilisateur']['ville'] = $membre[0]['ville'];
				$_SESSION['utilisateur']['cp'] = $membre[0]['cp'];
				$_SESSION['utilisateur']['adresse'] = $membre[0]['adresse'];
				$_SESSION['utilisateur']['statut'] = $membre[0]['statut'];
				//Connexion ok? redirection vers la page de profil
				header('location:profil.php');
				exit();
		} else {
				$msg = '<div class="erreur">Erreur d\'identifiant veuillez réessayer !</div>';
		}
	} else {
		$msg = '<div class="erreur">Erreur d\'identifiant veuillez réessayer !</div>';
	}
}
?>


<!-- Affichage du formulaire de connexion -->

<h1>Connexion à son compte</h1>
<div class="reaction">
    <p class="etiquette">Déjà membre ?</p>
    <form method="post">
        <div class="saisie">
            <div class="user clearfix">
                <div class="prenom">
                    <label for="email">Email</label>
                    <input type="text" value="" name="email" >
                </div>
                <div class="prenom">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" value="" name="mdp" >
                </div>
            </div>
        </div>
        <p class="etiquette">
            <button type="submit" name="connexion">Connexion</button>
        </p>
    </form>

    <a href="<?= URL ?>mdpperdu.php">Oubli de mot de passe ?</a>

</div>

<div class="reaction">
    <p class="etiquette">Pas encore membre ?</p>
    
    <a href="<?= URL ?>inscription.php">Inscrivez-vous</a>

</div>


<!-- Inclusion du fichier de footer -->

<?php
include_once 'inc/footer.inc.php';