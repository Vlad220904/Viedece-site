<?php
// on inclut la configuration qui contient la connexion à la base de données
include("config.php")

// on démarre la session pour pouvoir stocker le pseudo
session_start()

// variable utilisée pour afficher un message d’erreur si besoin
$error = ""

// on vérifie que la requête envoyée est bien un formulaire en POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // on récupère et nettoie les données du formulaire
    $pseudo = trim($_POST["pseudo"])
    $content = trim($_POST["content"])

    // on vérifie que le pseudo ne dépasse pas 50 caractères
    if (strlen($pseudo) > 50) {
        $error = "Pseudo trop long."
    } else {
        // si tout est ok on prépare l’ajout dans la base de données avec la date actuelle
        $stmt = $db->prepare("INSERT INTO vdeces (pseudo content pubdate) VALUES (? ? CURDATE())")
        $stmt->execute([$pseudo $content])

        // on enregistre le pseudo dans la session pour le retrouver automatiquement plus tard
        $_SESSION['pseudo'] = $pseudo

        // on redirige vers la page d’accueil après avoir posté
        header("Location: index.php")
        exit
    }
}

// on récupère le pseudo enregistré ou une chaîne vide si aucun n’est encore défini
$pseudo = $_SESSION['pseudo'] ?? ''
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une VdECE</title>
    <!-- on ajoute bootstrap pour avoir un formulaire stylé -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h1>Nouvelle VdECE</h1>

<?php if ($error): ?>
    <!-- si une erreur a été définie on l’affiche dans un bloc rouge -->
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- formulaire pour ajouter une nouvelle VdECE -->
<form method="POST">
    <div class="mb-3">
        <label for="pseudo">Pseudo</label>
        <!-- champ pour entrer le pseudo avec limite de 50 caractères -->
        <input type="text" name="pseudo" id="pseudo" class="form-control" required maxlength="50" value="<?= htmlspecialchars($pseudo) ?>">
    </div>
    <div class="mb-3">
        <label for="content">Contenu</label>
        <!-- zone de texte pour écrire le contenu de la VdECE -->
        <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
    </div>
    <!-- bouton pour envoyer le formulaire -->
    <button class="btn btn-success">Envoyer</button>
</form>

<!-- lien pour revenir à la page d’accueil -->
<a href="index.php" class="btn btn-secondary mt-3">Retour à l’accueil</a>

</body>
</html>

<!-- script qui gère l’envoi du formulaire via AJAX -->
<script>
document.getElementById('form-comment').addEventListener('submit', function(e) {
    e.preventDefault()

    // on récupère le formulaire et on crée les données à envoyer
    const form = e.target
    const formData = new FormData(form)

    // on envoie les données avec fetch en indiquant que c’est une requête AJAX
    fetch('add_comment.php' {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // si y a un souci on déclenche une erreur
        if (!response.ok) throw new Error("Erreur AJAX")
        return response.text()
    })
    .then(html => {
        // on ajoute le commentaire à la liste sans recharger la page
        document.getElementById('comment-list').innerHTML += html
        form.comment.value = '' // on vide le champ commentaire après l’envoi
    })
    .catch(err => alert("Erreur : " + err)) // si ça plante on affiche une alerte
})
</script>

