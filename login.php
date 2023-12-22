<?php
session_start();

require_once "includes/config.php"; // Remplacez par votre script de configuration de la base de données

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: profil.php");
    exit;
}

$email = $password = "";
$email_err = $password_err = "";

// Traitement des données du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifier si l'email est vide
    if (empty(trim($_POST["email"]))) {
        $email_err = "Veuillez entrer votre email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Vérifier si le mot de passe est vide
    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Valider les identifiants
    if (empty($email_err) && empty($password_err)) {
        // Préparer une déclaration select
        $sql = "SELECT id, email, password FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = $email;

            // Tenter d'exécuter la déclaration préparée
            if ($stmt->execute()) {
                // Vérifier si l'email existe, si oui, alors vérifier le mot de passe
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            // Le mot de passe est correct, démarrer une nouvelle session
                            session_start();
                            
                            // Stocker les données dans des variables de session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            
                            // Rediriger l'utilisateur vers la page de profil
                            header("location: profil.php");
                            exit;
                        } else {
                            // Rediriger vers la page d'accueil si le mot de passe est incorrect
                            header("location: index.php");
                            exit;
                        }
                    }
                } else {
                    // Rediriger vers la page d'accueil si l'email n'existe pas
                    header("location: index.php");
                    exit;
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }
    
    unset($pdo);
}
?>