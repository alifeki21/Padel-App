<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "padel_db";

// Pour gérer les erreurs lors de la connexion
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Définir le jeu de caractères en UTF8 pour supporter les accents
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Si la connexion échoue, on renvoie une erreur JSON
    header('Content-Type: application/json');
    die(json_encode([
        'status' => 'error',
        'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()
    ]));
}
?>
