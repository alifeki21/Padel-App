<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_name = $_POST['player_name'] ?? '';
    $player_phone = $_POST['player_phone'] ?? '';
    $report_type = $_POST['report_type'] ?? '';
    $indicated_level = !empty($_POST['indicated_level']) ? floatval($_POST['indicated_level']) : null;
    $real_level = !empty($_POST['real_level']) ? floatval($_POST['real_level']) : null;
    $reason = $_POST['reason'] ?? '';
    $behavior_details = $_POST['behavior_details'] ?? '';
    $reporter_id = $_SESSION['user_id'] ?? null;

    if (empty($player_name) || empty($report_type)) {
        echo json_encode(['status' => 'error', 'message' => 'Le nom du joueur et le type de signalement sont obligatoires.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO player_reports (reported_player_name, reported_player_phone, report_type, indicated_level, real_level, reason, behavior_details, reporter_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdsssi", $player_name, $player_phone, $report_type, $indicated_level, $real_level, $reason, $behavior_details, $reporter_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Signalement envoyé. Nous allons examiner la situation.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi du signalement.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
