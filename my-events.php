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

// Supprimer un fête
// Vérifier si l'ID de l'fête à supprimer est passé dans l'URL
if (isset($_GET["delete"]) && !empty(trim($_GET["delete"]))) {
    // Préparer une requête SQL pour supprimer l'fête
    $sql = "DELETE FROM events WHERE id = :id AND user_id = :user_id";

    if ($stmt = $pdo->prepare($sql)) {
        // Associer les paramètres ID et user_id à la requête
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
        $param_id = trim($_GET["delete"]);

        // Exécuter la requête. En cas de succès, rediriger l'utilisateur vers la page de ses fêtes
        if ($stmt->execute()) {
            header("location: my-events.php");
            exit();
        } else {
            // Afficher un message d'erreur en cas d'échec
            echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
        }
    }
    // Libérer le 'statement' préparé
    unset($stmt);
}

// Récupérer les fêtes créés par l'utilisateur
$sql = "SELECT id, title, image FROM events WHERE user_id = :user_id";
$events = [];

// Préparer et exécuter la requête pour récupérer les fêtes
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Récupérer les résultats et les ajouter au tableau $events
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row;
        }
    } else {
        // Afficher un message d'erreur en cas d'échec
        echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
    }
    // Libérer le 'statement' préparé
    unset($stmt);
}
// Libérer l'objet PDO de la connexion à la base de données
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FestiPlan</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/my-events.css">
</head>

<body>

    <?php include('includes/header.php'); ?> <!-- En-tête du site -->

    <div class="container">
        <h2>Mes fêtes</h2>
        <div class="event-container">
            <?php if (count($events) > 0) : ?>
                <?php foreach ($events as $event) : ?>
                    <div class="event">
                        <div>
                            <?php if (!empty($event["image"])) : ?>
                                <img class="image-rect" src="data:image/jpeg;base64,<?php echo $event["image"]; ?>" alt="Image de l'fête">
                            <?php else : ?>
                                <img src="/images/no-image.jpg" alt="Pas d'image disponible">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($event["title"]); ?>
                            <p>
                        </div>

                        <div class="buttons">
                            <a class="custom_button" href="event-details.php?id=<?php echo $event["id"]; ?>">Détails</a>
                            <a class="custom_button custom_button_delete" href="my-events.php?delete=<?php echo $event["id"]; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet fête ?');">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun fête à afficher.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?> <!-- Pied de page du site -->

</body>

</html>