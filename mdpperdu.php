<?php
 require_once 'inc/init_config.inc.php';
 require_once 'inc/header.inc.php';

//------------------------------------------------
$email = (!empty($_POST['email'])) ? strip_tags($_POST['email']) : '';
// $recup_mdp = $modelisation->query("SELECT mdp,email WHERE email= '$email' ;");

if(isset($_POST['valider'])) {

	if(preg_match('/@/', $email)){

		$check_email = $modelisation->prepare("SELECT email FROM membre WHERE email = :email");
		$check_email->bindValue(':email', $email, PDO::PARAM_STR);
		$check_email->execute();

		if($check_email->rowCount() > 0) { // si je trouve au moins 1 personne dans la BDD avec l'email saisi, le mdp est envoyé
			echo $email;

			function random($car) {
				$string = "";
				$chaine = "abcdefghijklmnpqrstuvwxy";
				srand((double)microtime()*1000000);

				for($i=0; $i<$car; $i++) {
					$string .= $chaine[rand()%strlen($chaine)];
				}

				return $string;
			}

			$chaine = random(10);
			$new_mdp = password_hash($chaine, PASSWORD_DEFAULT) ;

			$recup_mdp = $modelisation->prepare("UPDATE membre SET mdp='$new_mdp' WHERE email = :email");
			$recup_mdp->bindValue(':email', $email, PDO::PARAM_STR);
			$recup_mdp->execute();
			
			$destinataire= $email;
			$sujet= "Votre nouveau mot de passe";
			
			mail($destinataire,$sujet,"Nouveau mot de passe : ".$chaine);
			//echo $new_mdp;
			
			
			
		}else{ 
			$msg = '<div class="erreur">Cet email n\'existe pas</div>';
		}
	}else{
		$msg = '<div class="erreur">Votre email n\'est pas valide</div>';
	}
}

?>

	<?= (!empty($msg)) ? $msg : '' ?>
	<div>
		<p>Afin de pouvoir réinitialiser votre mot de passe, vous devez nous fournir votre adresse email : </p>
		<form method="post">
        <div class="saisie">
            <div class="user clearfix">
                <div class="prenom">
                    <label for="email">Email</label>
                    <input type="text" value="" name="email" >
                </div>
                
            </div>
        </div>
        <p class="etiquette">
            <button type="submit" name="valider">Valider</button>
        </p>
    </form>

	</div>



 <?php 
 include_once 'inc/footer.inc.php';