<?php
// paramètres de connexion à la base de données
$DB_HOST = 'localhost' // l’adresse du serveur mysql ici c’est la machine locale
$DB_NAME = 'viedece' // nom de la base qu’on veut utiliser
$DB_USER = 'root' // nom d’utilisateur par défaut en local
$DB_PASS = '' // mot de passe vide par défaut sur XAMPP

try {
    // on essaie de se connecter à la base avec PDO
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS)
} catch (Exception $e) {
    // si la connexion échoue on arrête tout et on affiche l’erreur
    die('Erreur : ' . $e->getMessage())
}
?>
