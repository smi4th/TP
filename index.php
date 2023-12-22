<?php
// Rediriger l'utilisateur vers la page de profil s'il est déjà connecté
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: profil.php");
    exit;
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
</head>

<body>

    <?php include('includes/header.php'); ?> <!-- En-tête du site -->

    <div class="container">
        <h2>Bienvenue sur le Planificateur de Fêtes</h2>

        <div class="form_container">
            <h3>Connecte-toi</h3>

            <form action="login.php" method="post"> <!-- Traiter la connexion dans login.php -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button class="custom_button" type="submit">Connexion</button>
            </form>

            <p class="link_redirection">Pas encore membre? <a href="inscription.php">Inscrivez-vous ici</a></p>
        </div>
    </div>

    <?php include('includes/footer.php'); ?> <!-- Pied de page du site -->

</body>

</html>