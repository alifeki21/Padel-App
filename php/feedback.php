<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $type = $_POST['type'] ?? 'general';
    $rating = intval($_POST['rating'] ?? 5);
    $message = $_POST['message'] ?? '';

    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Le message de feedback est obligatoire.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO feedback (user_email, type, rating, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $email, $type, $rating, $message);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Feedback envoyé avec succès ! Merci.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du feedback.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
