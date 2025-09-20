<?php
$conn = mysqli_connect("localhost","root","","oncf");
if (!$conn) {
    die("Error de connexion: " . mysqli_connect_error());
}
?>