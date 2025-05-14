<?php
// on inclut le fichier de config pour se connecter à la base
include("config.php")

// on vérifie que les champs id et type sont bien envoyés en POST
if (isset($_POST["id"]) && isset($_POST["type"])) {
    // on convertit l’id en entier pour éviter les erreurs
    $id = intval($_POST["id"])
    $type = $_POST["type"] // type d’action demandée support ou funny

    // si c’est un clic sur le bouton soutien
    if ($type === "support") {
        // on prépare la requête pour incrémenter le compteur de soutien
        $sql = "UPDATE vdeces SET support_count = support_count + 1 WHERE id = ?"
    } elseif ($type === "funny") {
        // sinon si c’est un clic sur drôle on incrémente funny_count
        $sql = "UPDATE vdeces SET funny_count = funny_count + 1 WHERE id = ?"
    } else {
        // si le type est invalide on renvoie vers l’accueil
        header("Location: index.php")
        exit
    }

    // on prépare et exécute la requête avec l’id reçu
    $stmt = $db->prepare($sql)
    $stmt->execute([$id])

    // après le vote on retourne sur la page du post concerné
    header("Location: show_vdece.php?id=$id")
    exit
}
?>
