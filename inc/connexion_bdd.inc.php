<?php
$dsn = 'mysql:host=localhost;dbname=modelisation';
$user = 'root';
$mdp = '';
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
];
$modelisation = new PDO($dsn,$user,$mdp,$options);