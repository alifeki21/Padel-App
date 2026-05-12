<?php
require_once __DIR__ . '/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (currentUser()) {
    header('Location: ../html/u_are_signed_in.html');
    exit;
}

$error    = '';
$emailVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $remember = isset($_POST['remember']);
    $emailVal = $email;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password === '') {
        $error = 'Password is required.';
    } else {
        try {
            $pdo  = getDbConnection();
            $stmt = $pdo->prepare(
                'SELECT id, first_name, last_name, email, password_hash
                 FROM users WHERE email = ? LIMIT 1'
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']    = (int)$user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name']  = $user['last_name'];
                $_SESSION['email']      = $user['email'];

                if ($remember) {
                    setcookie(
                        session_name(),
                        session_id(),
                        time() + 60 * 60 * 24 * 30,
                        '/'
                    );
                }

                header('Location: ../html/u_are_signed_in.html');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            error_log('Login DB error: ' . $e->getMessage());
            $error = 'A database error occurred. Please try again later.';
        }
    }
}

// Render the view — $error and $emailVal are in scope inside the template.
include __DIR__ . '/../html/login.html';
