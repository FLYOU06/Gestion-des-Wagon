<?php
session_start();
include 'db-conn.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['USERNAME']) && !isset($_COOKIE['USERNAME'])) {
    header("Location: unauthorized.php");
    exit();
}

$username = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : $_COOKIE['USERNAME'];

if (isset($_GET['id'])) {
    $wagon_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Vérifier si le wagon existe et récupérer ses informations
    $check_sql = "SELECT * FROM wagon WHERE NUM_WAGON = '$wagon_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $wagon_data = mysqli_fetch_assoc($check_result);
        $serie = $wagon_data['SERIE'];
        $dernier_vg = $wagon_data['DERNIER_DATE_VG'];
        $dernier_rl = $wagon_data['DERNIER_DATE_RL'];
        
        // 1. Marquer le wagon comme "supprimé" (ne pas le supprimer physiquement)
        $update_sql = "UPDATE wagon SET EST_SUPPRIME = 1 WHERE NUM_WAGON = '$wagon_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            // 2. Enregistrer l'action dans l'historique
            $action = "Marquage suppression wagon";
            $details = "Wagon $wagon_id marqué comme supprimé (Série: $serie, Dernier VG: $dernier_vg, Dernier RL: $dernier_rl)";
            
            $history_sql = "INSERT INTO historique (USERNAME, NUM_WAGON, ACTION, DETAILS) 
                            VALUES ('$username', '$wagon_id', '$action', '$details')";
            
            mysqli_query($conn, $history_sql);
            
            // Rediriger avec message de succès
            header("Location: index.php?delete_success=1&deleted_id=" . urlencode($wagon_id));
            exit();
        } else {
            $error_msg = "Erreur lors du marquage du wagon: " . mysqli_error($conn);
            header("Location: index.php?delete_error=" . urlencode($error_msg));
            exit();
        }
        
    } else {
        header("Location: index.php?delete_error=Wagon non trouvé: " . urlencode($wagon_id));
        exit();
    }
} else {
    header("Location: index.php?delete_error=Aucun ID de wagon spécifié");
    exit();
}

mysqli_close($conn);
?>