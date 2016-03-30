			<footer>
			<p><a href="<?= URL ?>mentions.php"> Mentions légales</a> | <a href="<?= URL?>cgv.php">C.G.V</a> | <a href="<?= URL ?>plan.php" >Plan du site</a> | <a href="javascript:window.print()" >Imprimer la page</a>  | <a href="<?= (isset($_SESSION['utilisateur'])) ? URL.'newsletter.php' : ''?>"> S’inscrire à la newsletter</a> | <a href="<?= URL ?>contact.php">Contact</a></p>
		</footer>
	</body>

	
</html>