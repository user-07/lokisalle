<?php
// Insertion du fichier de configuration:

require_once('inc/init_config.inc.php');

// Déclarations de variables pour éviter mes messages d'erreurs du navigateur:

$messageUser = (!empty($_POST['messageUser'])) ? strip_tags(trim($_POST['messageUser'])) : '';
$topicUser = (!empty($_POST['topicUser'])) ? strip_tags(trim($_POST['topicUser'])) : '';
$emailUser = (!empty($_POST['emailUser'])) ? strip_tags(trim($_POST['emailUser'])) : '';

// debug($_SESSION['utilisateur']['email']);

// Condition pour envoyer un message à l'administrateur si l'utilisateur est connecté
if (isset($_POST['sendMessage']) && isset($_SESSION['utilisateur'])) {
	if (!empty($messageUser)) {
		$messageSend= 'Message : '.$messageUser.' expediteur du message : '.$_SESSION['utilisateur']['email'];
		$messageSend = wordwrap($messageSend, 70, '\r\n'); # Saut de ligne tous les 70 caractères
		mail('admin@societe.com', $topicUser, $messageSend);
		echo 'Votre message a bien été envoyé et nous reviendrons vers vous dès que possible';
	}else{
		echo "Vous devez précisez le contenu de votre message !";
	}
// Condition pour envoyer un message à l'administrateur si l'utilisateur est visiteur:
}elseif (isset($_POST['sendMessage'])) {
	if (!empty($messageUser) && !empty($emailUser)) {
		$messageSend= 'Message : '.$messageUser.' expediteur du message : '.$emailUser;
		$messageSend = wordwrap($messageSend, 70, '\r\n'); # Saut de ligne tous les 70 caractères
		mail('admin@societe.com', $topicUser, $messageSend);
		echo 'Votre message a bien été envoyé et nous reviendrons vers vous dès que possible';
	}else{
		echo 'Vous devez précisez le contenu de votre message et votre adresse email !';
	}
}
?>


<!-- Affichage de formulaire de contact -->

<main>

	<form action="" method="post">
		<label for="topic">Sujet :</label>
		<input type="text" name="topicUser" id="topic">

		<label for="message">Message :</label>
		<textarea name="messageUser" id="message" cols="30" rows="10" required></textarea>

	<?php if (!isset($_SESSION['utilisateur'])) : ?>
		<label for="email">Expéditeur</label>
		<input type="email" name="emailUser" id="email" placeholder="exemple@domaine.com">
	<?php endif; ?>

		<input type="submit" name="sendMessage" value="Envoyer">

	</form>

</main>


<!-- Inclusion du fichier de pied de page -->

<?php
require_once('inc/footer.inc.php');