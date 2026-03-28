<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données POST
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $level = $_POST['level'] ?? 0;
    $position = $_POST['position'] ?? '';
    $hand = $_POST['hand'] ?? '';

    // Validation simple
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.']);
        exit;
    }

    // Hashage du mot de passe pour la sécurité
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Préparation de la requête pour éviter les injections SQL
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, skill_level, preferred_position, playing_hand) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdss", $first_name, $last_name, $email, $phone, $hashed_password, $level, $position, $hand);
        
        if ($stmt->execute()) {
            // L'utilisateur est connecté automatiquement après l'inscription
            $new_user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;

            echo json_encode([
                'status' => 'success',
                'message' => 'Compte créé avec succès !',
                'redirect' => 'login.html' // On peut rediriger vers login ou accueil
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Erreur de doublon sur l'email
            echo json_encode(['status' => 'error', 'message' => 'Cette adresse e-mail est déjà utilisée.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue lors de l\'inscription : ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
