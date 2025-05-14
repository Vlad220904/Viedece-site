<?php
// on inclut le fichier de config pour se connecter à la base
include("config.php")

// on récupère la page actuelle depuis l’URL sinon on prend la page 1
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1

// nombre de posts à afficher par page
$per_page = 5

// on calcule à partir de combien on doit commencer à lire dans la base
$offset = ($page - 1) * $per_page

// on prépare une requête pour récupérer les posts avec le nombre de commentaires
$stmt = $db->prepare("SELECT v.*, COUNT(c.id) as nb_comments
    FROM vdeces v
    LEFT JOIN comments c ON v.id = c.vde_id
    GROUP BY v.id
    ORDER BY v.pubdate DESC
    LIMIT :offset, :limit")

// on passe les valeurs offset et limit dans la requête pour éviter les injections
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT)
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT)

// on exécute la requête
$stmt->execute()

// on récupère tous les résultats dans un tableau
$vdes = $stmt->fetchAll()
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vie d’ECE</title>
    <!-- on ajoute bootstrap pour le style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- on ajoute une police que nous avons choisis -->
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
<style>
  /* style de base pour le fond et la police */
  body {
    font-family: 'Comic Neue' cursive;
    background-color: #f7f7f7;
  }
</style>
</head>
<body class="container mt-4">

<!-- logo ECE centré en haut de la page -->
<div class="text-center mb-4">
    <img src="ece_logo.png" alt="Logo ECE" style="max-width: 200px;">
</div>

<h1 class="text-center">Vie d’ECE</h1>

<!-- bouton pour aller écrire une nouvelle VdECE -->
<a href="add_vdece.php" class="btn btn-primary mb-3">Ajouter une VdECE</a>

<!-- affichage des VdECE récupérées dans la boucle -->
<?php foreach ($vdes as $vde): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5><?= htmlspecialchars($vde['pseudo']) ?> — <?= $vde['pubdate'] ?></h5>
            <p><?= nl2br(htmlspecialchars($vde['content'])) ?></p>
            <!-- lien vers la page détaillée avec les commentaires -->
            <a href="show_vdece.php?id=<?= $vde['id'] ?>"><?= $vde['nb_comments'] ?> commentaires</a>
        </div>
    </div>
<?php endforeach; ?>

<!-- zone de pagination pour changer de page -->
<nav>
  <ul class="pagination">
    <?php for ($i = 1; $i <= 10; $i++): ?>
      <li class="page-item<?= $i == $page ? ' active' : '' ?>">
        <a class="page-link" href="index.php?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

</body>
</html>
