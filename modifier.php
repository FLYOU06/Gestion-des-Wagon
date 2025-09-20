<?php
session_start();
include 'db-conn.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['USERNAME']) && !isset($_COOKIE['USERNAME'])) {
    header("Location: unauthorized.php");
    exit();
}
$USERNAME = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : $_COOKIE['USERNAME'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $dernierVG = mysqli_real_escape_string($conn, $_POST['dernier_vg']);
    $dernierRL = mysqli_real_escape_string($conn, $_POST['dernier_rl']);

    $sql = "UPDATE wagon 
            SET DERNIER_DATE_VG = '$dernierVG', 
                DERNIER_DATE_RL = '$dernierRL' 
            WHERE num_wagon = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Popup success
        $histo = "INSERT INTO historique (USERNAME , action, num_wagon, details) 
                VALUES ('$USERNAME','Modifier', '$id', 'Dates modifiées: VG=$dernierVG, RL=$dernierRL')";
        mysqli_query($conn, $histo);
        echo "<script>
                alert('✅ Wagon mis à jour avec succès !');
                window.location.href='index.php';
              </script>";
    } else {
        echo "❌ Erreur: " . mysqli_error($conn);
    }
}
?>
