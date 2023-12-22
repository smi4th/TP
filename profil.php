<?php
// Démarrer la session pour avoir accès aux variables de session
session_start();
// Inclure le fichier de configuration (contenant par exemple des paramètres de connexion à la base de données)
require_once "includes/config.php";

// Vérifier si l'utilisateur est déjà connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Traitement lors de la réception d'une méthode POST (soumission de formulaire) avec une image téléchargée
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    // Lire les données de l'image téléchargée et les encoder en base64
    $imageData = file_get_contents($_FILES["profile_image"]["tmp_name"]);
    $encodedImage = base64_encode($imageData);

    // Préparation de la requête SQL pour mettre à jour l'image de l'utilisateur dans la base de données
    $update_sql = "UPDATE users SET image = :image WHERE id = :id";
    if ($update_stmt = $pdo->prepare($update_sql)) {
        // Associer les valeurs aux paramètres et exécuter la requête
        $update_stmt->bindParam(":image", $encodedImage, PDO::PARAM_LOB);
        $update_stmt->bindParam(":id", $_SESSION["id"], PDO::PARAM_INT);
        $update_stmt->execute();
    }
}

// Récupérer les informations de l'utilisateur depuis la base de données
$user_id = $_SESSION["id"];
$sql = "SELECT pseudo, first_name, last_name, email, image FROM users WHERE id = :id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            // Stocker les informations de l'utilisateur dans des variables si trouvé
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_pseudo = $row["pseudo"];
            $user_first_name = $row["first_name"];
            $user_last_name = $row["last_name"];
            $user_email = $row["email"];
            $user_image = $row["image"];
        } else {
            // Afficher une erreur si l'utilisateur n'est pas trouvé
            echo "Erreur! Utilisateur non trouvé.";
            exit;
        }
    } else {
        // Afficher une erreur si la requête échoue
        echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
    }
    // Libérer le statement
    unset($stmt);
}
// Fermer la connexion à la base de données
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>FestiPlan</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profil.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Salut <b><?php echo htmlspecialchars($user_pseudo); ?></b>. Bienvenue sur ton profil</h2>
        <div class="contenu">

            <div class="container_image">
                <?php if (!empty($user_image)) : ?>
                    <img src="data:image/jpeg;base64,<?php echo $user_image; ?>" alt="Image de profil" />
                <?php else : ?>
                    <img src="images/no-image.jpg" alt="Pas d'image disponible" />
                <?php endif; ?>


                <form action="profil.php" method="post" enctype="multipart/form-data">
                    <label for="fileInput" class="custom-file-input">Selectionne une photo</label>
                    <input type="file" id="fileInput" name="profile_image" accept="image/*" style="display: none;">
                    <img id="imagePreview" src="" alt="Aperçu de l'image" style="max-width: 200px; max-height: 200px; display: none;" />
                    <button type="submit" class="custom-file-input" id="submitButton">Mettre à jour la photo</button>
                </form>
            </div>

            <div class="info">
                <p>
                    <b>Prénom :</b> <?php echo htmlspecialchars($user_first_name); ?><br>
                    <b>Nom :</b> <?php echo htmlspecialchars($user_last_name); ?><br>
                    <b>Email :</b> <?php echo htmlspecialchars($user_email); ?><br>
                </p>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="js/profil.js"></script>

</body>

</html>