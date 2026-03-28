<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour réserver.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $court_number = $_POST['court_number'] ?? '';
    $reservation_date = $_POST['reservation_date'] ?? '';
    $reservation_time = $_POST['reservation_time'] ?? '';
    $duration = 90; // Fixée à 90m (1.5h) dans le JS
    $price = $_POST['price'] ?? 0;

    if (empty($court_number) || empty($reservation_date) || empty($reservation_time)) {
        echo json_encode(['status' => 'error', 'message' => 'Informations de réservation incomplètes.']);
        exit;
    }

    try {
        // Optionnel : vérifier si le créneau est déjà pris
        $check = $conn->prepare("SELECT id FROM reservations WHERE court_number = ? AND reservation_date = ? AND reservation_time = ? AND status != 'cancelled'");
        $check->bind_param("iss", $court_number, $reservation_date, $reservation_time);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Ce créneau est déjà réservé.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO reservations (user_id, court_number, reservation_date, reservation_time, duration, price, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
        $stmt->bind_param("iissid", $user_id, $court_number, $reservation_date, $reservation_time, $duration, $price);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Réservation confirmée !']);
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la réservation : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
