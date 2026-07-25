<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_soutenance_504";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>