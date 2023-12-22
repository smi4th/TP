<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "includes/config.php";

// Définir des variables et les initialiser avec des valeurs vides
$title = $description = $event_date = $location = $image = "";
$is_public = 1; // Valeur par défaut pour l'fête public
$title_err = $description_err = $event_date_err = $location_err = $image_err = "";

// Traitement des données du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Valider le titre
    if (empty(trim($_POST["title"]))) {
        $title_err = "Veuillez entrer un titre.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Valider la description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Veuillez entrer une description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Valider la date de l'fête
    if (empty(trim($_POST["event_date"]))) {
        $event_date_err = "Veuillez entrer une date pour l'fête.";
    } else {
        $event_date = trim($_POST["event_date"]);
    }

    // Valider le lieu
    if (empty(trim($_POST["location"]))) {
        $location_err = "Veuillez entrer un lieu pour l'fête.";
    } else {
        $location = trim($_POST["location"]);
    }

    // Gérer la case à cocher is_public
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    // Valider et traiter l'image téléchargée
    if (isset($_FILES["profile_image"])) {
        if ($_FILES["profile_image"]["error"] == 4) {
            // Pas de fichier téléchargé, traiter comme optionnel
            $image = NULL;
        } elseif ($_FILES["profile_image"]["error"] != 0) {
            // Gérer les erreurs de téléchargement
            $image_err = "Erreur lors du téléchargement du fichier. Code d'erreur: " . $_FILES["profile_image"]["error"];
        } else {
            $file = $_FILES["profile_image"];
            $allowedMimeTypes = ['image/jpeg', 'image/png'];

            if (!in_array($file['type'], $allowedMimeTypes)) {
                $image_err = "Type de fichier non autorisé. Seuls JPEG et PNG sont acceptés.";
            } elseif ($file['size'] > 5000000) { // 5MB max
                $image_err = "Le fichier est trop volumineux. Taille maximale autorisée : 5MB.";
            } else {
                $imageData = file_get_contents($file['tmp_name']);
                $image = base64_encode($imageData);
            }
        }
    }

    // Vérifier les erreurs avant d'insérer dans la base de données
    if (empty($title_err) && empty($description_err) && empty($event_date_err) && empty($location_err) && empty($image_err)) {
        // Préparer une déclaration d'insertion
        $sql = "INSERT INTO events (user_id, title, description, event_date, location, is_public, image) VALUES (:user_id, :title, :description, :event_date, :location, :is_public, :image)";

        if ($stmt = $pdo->prepare($sql)) {
            // Lier les variables à la déclaration préparée en tant que paramètres
            $stmt->bindParam(":user_id", $param_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":description", $param_description, PDO::PARAM_STR);
            $stmt->bindParam(":event_date", $param_event_date, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":is_public", $param_is_public, PDO::PARAM_INT);
            $stmt->bindParam(":image", $param_image, $image === NULL ? PDO::PARAM_NULL : PDO::PARAM_LOB);

            // Définir les paramètres
            $param_user_id = $_SESSION["id"];
            $param_title = $title;
            $param_description = $description;
            $param_event_date = $event_date;
            $param_location = $location;
            $param_is_public = $is_public;
            $param_image = $image;

            // Tenter d'exécuter la déclaration préparée
            if ($stmt->execute()) {
                // Rediriger vers la liste des fêtes ou une page de confirmation
                header("location: event-list.php");
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
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
    <link rel="stylesheet" href="css/creation-event.css">
</head>

<body>

    <?php include('includes/header.php'); ?> <!-- En-tête du site -->

    <div class="container">
        <h2>Créer un Nouvel fête</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div>
                <label>Titre du le fête</label>
                <input type="text" name="title" maxlength="50" value="<?php echo $title; ?>">
                <span><?php echo $title_err; ?></span>
            </div>
            <div>
                <label>Descripton</label>
                <textarea name="description" rows="5" collumn="3"><?php echo $description; ?></textarea>
                <span><?php echo $description_err; ?></span>
            </div>
            <div>
                <label>Date du le fête</label>
                <input type="datetime-local" name="event_date" value="<?php echo $event_date; ?>">
                <span><?php echo $event_date_err; ?></span>
            </div>
            <div>
                <label>Liieu</label>
                <input type="text" name="location" maxlength="60" value="<?php echo $location; ?>">
                <span><?php echo $location_err; ?></span>
            </div>
            <div>
                <label>fête Public</label>
                <input type="checkbox" name="is_public" <?php echo $is_public ? 'checked' : ''; ?>>
            </div>
            <div>
                <label>Image du le fête</label>
                <label for="fileInput" class="custom-file-input">Selectionne une photo</label>
                <input type="file" id="fileInput" name="profile_image" accept="image/*" style="display: none;">
                <span><?php echo $image_err; ?></span>
            </div>

            <div>
                <img id="imagePreview" src="" alt="Aperçu de l'image" style="max-width: 200px; max-height: 200px; display: none;" />
            </div>

            <div class="container_submit_button">
                <button class="custom_button" type="submit">Créer l'évènement</button>
            </div>
        </form>
    </div>

    <?php include('includes/footer.php'); ?> <!-- Pied de page du site -->

    <script src="js/create-event.js"></script>

</body>

</html>