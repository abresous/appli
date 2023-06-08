<?php
//Création/Ouverture de session :
session_start(); 


//---------------------------------------
//Connexion à la BDD :
$pdo = new PDO('mysql:host=localhost;dbname=appli', 'root', '', array( PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8" ) );

//---------------------------------------
//Définition d'une contante :
define('URL', 'http://localhost/appli/');

//---------------------------------------
//Définition de variables :
$content = '';
$error = '';

//---------------------------------------
//Inclusion des fonctions :
require_once 'fonction.inc.php';
