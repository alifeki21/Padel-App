<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Vérification du mot de passe haché
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                echo json_encode(['status' => 'success', 'message' => 'Connexion réussie !', 'redirect' => 'acceuil.html']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'E-mail ou mot de passe incorrect.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aucun compte trouvé avec cet e-mail.']);
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue lors de la connexion : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
