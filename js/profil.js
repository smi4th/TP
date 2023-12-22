// Écouter l'fête 'DOMContentLoaded', qui indique que le contenu du document a été chargé
document.addEventListener("DOMContentLoaded", function() {
    // Récupérer les éléments du DOM par leurs identifiants
    var fileInput = document.getElementById('fileInput'); // Champ de saisie de fichier
    var imagePreview = document.getElementById('imagePreview'); // Élément pour afficher l'aperçu de l'image
    var submitButton = document.getElementById('submitButton'); // Bouton de soumission

    // Masquer initialement le bouton de soumission
    submitButton.style.display = 'none';

    // Ajouter un gestionnaire d'fêtes pour réagir aux changements du champ de saisie de fichier
    fileInput.addEventListener('change', function() {
        // Vérifier si un fichier est sélectionné
        if (this.files && this.files[0]) {
            var reader = new FileReader(); // Créer un objet FileReader pour lire le fichier

            reader.onload = function(e) {
                // Mettre à jour la source de l'élément d'aperçu d'image avec les données du fichier
                imagePreview.src = e.target.result;
                // Afficher l'élément d'aperçu d'image
                imagePreview.style.display = 'block';
                // Afficher le bouton de soumission et l'activer
                submitButton.style.display = 'block';
                submitButton.disabled = false;
            };

            // Lire le contenu du fichier sélectionné
            reader.readAsDataURL(this.files[0]);
        } else {
            // Masquer l'élément d'aperçu d'image et le bouton de soumission si aucun fichier n'est sélectionné
            imagePreview.style.display = 'none';
            submitButton.style.display = 'none';
            submitButton.disabled = true; // Désactiver le bouton
        }
    });
});
