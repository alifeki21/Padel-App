<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour vous inscrire à un tournoi.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournament_id = $_POST['tournament_id'] ?? '';
    $partner_name = $_POST['partner_name'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (empty($tournament_id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID du tournoi manquant.']);
        exit;
    }

    try {
        // Vérifier si le tournoi existe et s'il reste des places
        $check_stmt = $conn->prepare("SELECT current_teams, max_teams FROM tournaments WHERE id = ?");
        $check_stmt->bind_param("i", $tournament_id);
        $check_stmt->execute();
        $tournament = $check_stmt->get_result()->fetch_assoc();

        if (!$tournament) {
            echo json_encode(['status' => 'error', 'message' => 'Tournoi introuvable.']);
            exit;
        }

        if ($tournament['current_teams'] >= $tournament['max_teams']) {
            echo json_encode(['status' => 'error', 'message' => 'Ce tournoi est déjà complet.']);
            exit;
        }

        // Inscrire l'utilisateur
        $stmt = $conn->prepare("INSERT INTO tournament_registrations (tournament_id, user_id, partner_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $tournament_id, $user_id, $partner_name);
        
        if ($stmt->execute()) {
            // Mettre à jour le nombre d'équipes actuelles
            $conn->query("UPDATE tournaments SET current_teams = current_teams + 1 WHERE id = $tournament_id");
            echo json_encode(['status' => 'success', 'message' => 'Inscription au tournoi réussie !']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'inscription.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
