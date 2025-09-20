<?php
session_start();
include 'db-conn.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['USERNAME']) && !isset($_COOKIE['USERNAME'])) {
    header("Location: unauthorized.php");
    exit();
}
// Vérifier si formulaire soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $serie = mysqli_real_escape_string($conn, $_POST['serie']);
    $dernierVG = mysqli_real_escape_string($conn, $_POST['dernierVG']);
    $dernierRL = mysqli_real_escape_string($conn, $_POST['dernierRL']);
    $USERNAME = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : $_COOKIE['USERNAME'];

    $sql = "INSERT INTO wagon (NUM_WAGON, DERNIER_DATE_VG, DERNIER_DATE_RL, SERIE) 
            VALUES ('$id', '$dernierVG', '$dernierRL', '$serie')";

    if (mysqli_query($conn, $sql)) {
        $histo = "INSERT INTO historique (USERNAME, ACTION, NUM_WAGON, DETAILS) 
                  VALUES ('$USERNAME', 'Ajouter', '$id', 'Wagon ajouté avec série: $serie')";
        mysqli_query($conn, $histo);
        echo "<script>
                alert('✅ Wagon ajouté avec succès !');
                window.location.href='index.php';
              </script>";
    } else {
        echo "❌ Erreur: " . mysqli_error($conn);
    }
}
?>
