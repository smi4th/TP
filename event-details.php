<?php
// Démarrer une session PHP pour pouvoir utiliser les variables de session
session_start();
// Inclure le fichier d'en-tête (header.php) qui peut contenir du HTML ou d'autres éléments nécessaires en début de page
include("includes/header.php");

// Vérifier si l'utilisateur est connecté en examinant les variables de session
// S'il n'est pas connecté, le rediriger vers la page de connexion
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Inclure le fichier de configuration (config.php) pour accéder, par exemple, aux paramètres de la base de données
require_once "includes/config.php";

// Vérifier si l'ID de l'événement est présent dans l'URL (via GET) et s'assurer qu'il n'est pas vide
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Préparer une requête SQL pour récupérer les détails de l'événement spécifique
    // La requête joint les tables 'events' et 'users' pour récupérer des informations supplémentaires sur l'utilisateur qui a créé l'événement
    $sql = "SELECT events.*, users.image AS user_image, users.pseudo AS user_pseudo FROM events LEFT JOIN users ON events.user_id = users.id WHERE events.id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        // Associer le paramètre ':id' à la valeur d'ID obtenue via GET
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

        // Définir la valeur du paramètre ID après avoir éliminé les espaces superflus
        $param_id = trim($_GET["id"]);

        // Tenter d'exécuter la requête préparée
        if ($stmt->execute()) {
            // Vérifier si la requête a retourné une ligne, ce qui signifie que l'événement existe
            if ($stmt->rowCount() == 1) {
                // Récupérer les données de l'événement sous forme de tableau associatif
                $event = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // Si aucun événement n'est trouvé avec cet ID, rediriger vers une page d'erreur
                header("location: error.php");
                exit();
            }
        } else {
            // Afficher un message d'erreur en cas d'échec de l'exécution de la requête
            echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
        }
    }

    // Libérer le 'statement' préparé et la variable de connexion PDO
    unset($stmt);
    unset($pdo);
} else {
    // Rediriger vers une page d'erreur si l'ID de l'événement n'est pas spécifié dans l'URL
    header("location: error.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FestiPlan</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/event-details.css">
</head>

<body>
    <div class="container">
        <h2>Détails de l'Événement<br><?php echo htmlspecialchars($event["title"]); ?></h2>
        <!-- Image de l'événement -->
        <?php if (!empty($event["image"])) : ?>
            <img class="event_image" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'événement" style="width:100%;height:auto;">
        <?php endif; ?>

        <!-- Image de profil de l'utilisateur -->
        <div class="author">
            <?php if (!empty($event["user_image"])) : ?>
                <img src="data:image/jpeg;base64,<?php echo $event["user_image"]; ?>" alt="Image de profil de l'utilisateur" style="width:100px;height:100px;">
            <?php endif; ?>
            <p>évènement de <br><?php echo htmlspecialchars($event["user_pseudo"]); ?></p>
        </div>

        <p class="description"><b>Description :</b> <?php echo htmlspecialchars($event["description"]); ?></p>

        <p class="localisation">L'évènement a lieu à <b><?php echo htmlspecialchars($event["location"]); ?></b>
        le <b><?php echo htmlspecialchars($event["event_date"]); ?></b>.</p>

        <p class="type">Il s'agit d'un évènement <b><?php echo $event["is_public"] ? 'Public' : 'Privé'; ?></b> !</p>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>