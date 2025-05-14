<?php
// on inclut le fichier config pour la connexion à la base de données
include("config.php")

// on démarre la session pour récupérer le pseudo si besoin
session_start()

// si aucun id n’est fourni on affiche une erreur 404
if (!isset($_GET["id"])) {
    http_response_code(404)
    echo "Erreur 404 : VdECE non trouvée."
    exit
}

// on convertit l’id reçu en entier
$id = intval($_GET["id"])

// on récupère les infos de la VdECE correspondante
$stmt = $db->prepare("SELECT * FROM vdeces WHERE id = ?")
$stmt->execute([$id])
$vde = $stmt->fetch(PDO::FETCH_ASSOC)

// si la VdECE n’existe pas on affiche une erreur 404
if (!$vde) {
    http_response_code(404)
    echo "Erreur 404 : VdECE non trouvée."
    exit
}

// on récupère tous les commentaires liés à cette VdECE classés par date
$comments = $db->prepare("SELECT * FROM comments WHERE vde_id = ? ORDER BY pubdate ASC")
$comments->execute([$id])
$all_comments = $comments->fetchAll()

// on récupère le pseudo de la session s’il existe
$pseudo = $_SESSION['pseudo'] ?? ''
?>

<!DOCTYPE html>
<html>
<head>
    <title>VdECE #<?= $vde['id'] ?></title>
    <!-- lien vers Bootstrap pour le style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<!-- on affiche le pseudo et la date de publication -->
<h2><?= htmlspecialchars($vde['pseudo']) ?> — <?= $vde['pubdate'] ?></h2>

<!-- contenu de la VdECE avec sa mise en forme -->
<p><?= nl2br(htmlspecialchars($vde['content'])) ?></p>

<!-- bouton pour ajouter un vote de compassion -->
<form method="POST" action="react.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $vde['id'] ?>">
    <input type="hidden" name="type" value="support">
    <button type="submit" class="btn btn-outline-danger">
        😭 Je compatis (<?= $vde['support_count'] ?>)
    </button>
</form>

<!-- bouton pour voter que c’est drôle -->
<form method="POST" action="react.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $vde['id'] ?>">
    <input type="hidden" name="type" value="funny">
    <button type="submit" class="btn btn-outline-warning">
        😂 Trop drôle (<?= $vde['funny_count'] ?>)
    </button>
</form>

<hr>

<!-- affichage des commentaires existants -->
<h4>Commentaires</h4>
<div id="comment-list">
<?php foreach ($all_comments as $c): ?>
    <div class="mb-3">
        <strong><?= htmlspecialchars($c['pseudo']) ?> :</strong>
        <p><?= nl2br(htmlspecialchars($c['content'])) ?> <br><em><?= $c['pubdate'] ?></em></p>
    </div>
<?php endforeach; ?>
</div>

<hr>

<!-- formulaire pour ajouter un nouveau commentaire -->
<h4>Ajouter un commentaire</h4>
<form method="POST" action="add_comment.php" id="form-comment">
    <input type="hidden" name="vde_id" value="<?= $vde['id'] ?>">
    <div class="mb-3">
        <label>Pseudo</label>
        <input type="text" name="pseudo" class="form-control" maxlength="50" required value="<?= htmlspecialchars($pseudo) ?>">
    </div>
    <div class="mb-3">
        <label>Commentaire</label>
        <textarea name="comment" class="form-control" rows="3" required></textarea>
    </div>
    <button class="btn btn-primary">Envoyer</button>
</form>

<!-- lien pour revenir à la page d’accueil -->
<a href="index.php" class="btn btn-secondary mt-3">Retour à l’accueil</a>

<!-- script pour gérer l’envoi du commentaire en AJAX -->
<script>
document.getElementById('form-comment').addEventListener('submit', function(e) {
    e.preventDefault()

    const form = e.target
    const formData = new FormData(form)

    fetch('add_comment.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // on ajoute le nouveau commentaire à la fin de la liste
        document.getElementById('comment-list').innerHTML += html
        form.comment.value = '' // on vide le champ texte
    })
    .catch(err => alert("Erreur réseau : " + err))
})
</script>

</body>
</html>

