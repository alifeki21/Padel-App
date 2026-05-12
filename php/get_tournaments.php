<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $result = $conn->query("SELECT * FROM tournaments WHERE status = 'upcoming' ORDER BY event_date ASC");
    $tournaments = [];
    
    while ($row = $result->fetch_assoc()) {
        $tournaments[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $tournaments]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération des tournois : ' . $e->getMessage()]);
}
?>
