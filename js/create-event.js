// Écouter l'événement 'DOMContentLoaded' qui est déclenché lorsque le contenu initial du document HTML a été complètement chargé et analysé
document.addEventListener("DOMContentLoaded", function() {
    // Obtenir l'élément de saisie de fichier et l'élément d'aperçu d'image par leurs identifiants
    var fileInput = document.getElementById('fileInput');
    var imagePreview = document.getElementById('imagePreview');

    // Ajouter un gestionnaire d'événements pour réagir au changement du champ de saisie de fichier
    fileInput.addEventListener('change', function() {
        // Vérifier si un fichier est sélectionné
        if (this.files && this.files[0]) {
            // Créer un nouvel objet FileReader pour lire le contenu du fichier sélectionné
            var reader = new FileReader();

            // Définir ce qui se passe lorsque le FileReader a fini de lire le fichier
            reader.onload = function(e) {
                // Mettre à jour la source de l'élément d'image avec les données du fichier lu
                imagePreview.src = e.target.result;
                // Afficher l'élément d'aperçu d'image
                imagePreview.style.display = 'block';
            };

            // Démarrer la lecture du fichier sélectionné comme une URL de données
            reader.readAsDataURL(this.files[0]);
        } else {
            // Masquer l'élément d'aperçu d'image s'il n'y a pas de fichier sélectionné
            imagePreview.style.display = 'none';
        }
    });
});
