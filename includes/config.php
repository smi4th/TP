<?php
// Paramètres de connexion à la base de données
define('DB_SERVER', 'localhost');    // Serveur de la base de données
define('DB_USERNAME', 'root');   // Nom d'utilisateur de la base de données
define('DB_PASSWORD', '');   // Mot de passe de la base de données
define('DB_NAME', 'party_planner_db'); // Nom de la base de données

/* Tentative de connexion à la base de données MySQL */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Définit le mode d'erreur PDO sur exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERREUR : Impossible de se connecter. " . $e->getMessage());
}
?>
