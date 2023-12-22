<?php
// Démarrer une session PHP pour accéder aux variables de session
session_start();

// Vérifier si l'utilisateur est connecté. Si non, le rediriger vers la page de connexion
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Inclure le fichier de configuration pour la connexion à la base de données
require_once "includes/config.php";

// Préparer une requête SQL pour récupérer les événements depuis la base de données
// Les événements sont triés par date de manière décroissante
$sql = "SELECT id, title, description, event_date, location, is_public, image FROM events ORDER BY event_date DESC";
$events = []; // Initialisation d'un tableau vide pour stocker les événements

// Exécuter la requête SQL et remplir le tableau $events avec les résultats
if ($result = $pdo->query($sql)) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $events[] = $row; // Ajouter chaque ligne de résultat dans le tableau $events
    }
    unset($result); // Libérer le résultat de la requête
} else {
    // Afficher un message d'erreur si la requête échoue
    echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
}

// Libérer l'objet PDO de la connexion à la base de données
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Événements</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/event-list.css">
</head>

<body>

    <?php include('includes/header.php'); ?> <!-- En-tête du site -->

    <div class="container">
        <h2>FestiPlan</h2>
        <div class="event-container">
            <?php if (count($events) > 0) : ?>
                <?php foreach ($events as $event) : ?>
                    <a href="event-details.php?id=<?php echo $event["id"]; ?>" class="event-card-link">
                        <div class="event-card">
                            <div class="top">
                                <h3><?php echo htmlspecialchars($event["title"]); ?></h3>
                                <div class="image-container"> <!-- Ajout d'un conteneur pour l'image -->
                                    <?php if (!empty($event["image"])) : ?>
                                        <img class="image-rect" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'événement">
                                    <?php else : ?>
                                        <img src="/images/no-image.jpg" alt="Pas d'image disponible">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bottom">
                                <p>Lieu: <?php echo htmlspecialchars($event["location"]); ?></p>
                                <p>Date: <?php echo htmlspecialchars($event["event_date"]); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun événement à afficher.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?> <!-- Pied de page du site -->

</body>

</html>