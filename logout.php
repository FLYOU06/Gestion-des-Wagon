<?php
session_start();
// Nettoyer toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Supprimer les cookies de connexion
if (isset($_COOKIE['USERNAME'])) {
    setcookie('USERNAME', '', time() - 3600, '/');
}

if (isset($_COOKIE['NOM'])) {
    setcookie('NOM', '', time() - 3600, '/');
}

if (isset($_COOKIE['ROLE'])) {
    setcookie('ROLE', '', time() - 3600, '/');
}

if (isset($_COOKIE['MDP'])) {
    setcookie('MDP', '', time() - 3600, '/');
}

// Rediriger vers la page de connexion
header("Location: login.php");
exit;
?>