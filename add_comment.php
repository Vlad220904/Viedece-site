<?php
// active l’affichage des erreurs pour le debug
ini_set('display_errors' 1)
ini_set('display_startup_errors' 1)
error_reporting(E_ALL)

// on inclut la config du site pour la base de données
include("config.php")

// démarre la session pour pouvoir garder le pseudo par exemple
session_start()

// on vérifie que la requête est bien un envoi de formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // on récupère les données envoyées par le formulaire
    $vde_id = intval($_POST["vde_id"]) // id du post on le met en entier pour être sûr
    $pseudo = trim($_POST["pseudo"]) // on enlève les espaces au début et à la fin
    $comment = trim($_POST["comment"]) // pareil pour le commentaire

    // on vérifie que tous les champs sont remplis sinon on arrête
    if (!$vde_id || !$pseudo || !$comment) {
        http_response_code(400) // erreur car il manque des infos
        echo "Données manquantes."
        exit
    }

    // si le pseudo est trop long on bloque
    if (strlen($pseudo) > 50) {
        http_response_code(400)
        echo "Pseudo trop long."
        exit
    }

    // on garde le pseudo en session pour l'afficher plus tard automatiquement
    $_SESSION["pseudo"] = $pseudo

    // on insère le commentaire dans la base avec la date du jour
    $stmt = $db->prepare("INSERT INTO comments (vde_id pseudo content pubdate) VALUES (? ? ? CURDATE())")
    $stmt->execute([$vde_id $pseudo $comment])

    // on vérifie si la requête vient d’un appel ajax
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'

    if ($is_ajax) {
        // si c’est ajax on renvoie juste le nouveau commentaire sans recharger la page
        echo '<div class="mb-3">'
        echo '<strong>' . htmlspecialchars($pseudo) . ' :</strong>'
        echo '<p>' . nl2br(htmlspecialchars($comment)) . '<br><em>' . date("Y-m-d") . '</em></p>'
        echo '</div>'
        exit
    } else {
        // sinon on retourne sur la page du post avec le commentaire ajouté
        header("Location: show_vdece.php?id=" . $vde_id)
        exit
    }
}

