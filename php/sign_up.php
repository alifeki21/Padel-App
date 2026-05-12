<?php
require_once __DIR__ . '/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (currentUser()) {
    header('Location: ../html/u_are_signed_in.html');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName     = trim($_POST['firstName'] ?? '');
    $lastName      = trim($_POST['lastName']  ?? '');
    $email         = trim($_POST['email']     ?? '');
    $phone         = trim($_POST['phone']     ?? '');
    $passwordInput = $_POST['password']        ?? '';
    $confirm       = $_POST['confirmPassword'] ?? '';
    $skillLevel    = floatval($_POST['level']  ?? 0);
    $position      = $_POST['position']        ?? '';
    $hand          = $_POST['hand']            ?? '';
    $terms         = isset($_POST['terms']);

    if (!$firstName)                                    $error = 'First name is required.';
    elseif (strlen($firstName) < 2)                     $error = 'First name must be at least 2 characters.';
    elseif (!$lastName)                                 $error = 'Last name is required.';
    elseif (strlen($lastName) < 2)                      $error = 'Last name must be at least 2 characters.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email address.';
    elseif (!$phone)                                    $error = 'Phone number is required.';
    elseif (strlen($passwordInput) < 8)                 $error = 'Password must be at least 8 characters.';
    elseif (!preg_match('/[a-z]/', $passwordInput) ||
            !preg_match('/[A-Z]/', $passwordInput) ||
            !preg_match('/\d/',    $passwordInput) ||
            !preg_match('/[\W_]/', $passwordInput))     $error = 'Password must contain uppercase, lowercase, number and special character.';
    elseif ($passwordInput !== $confirm)                $error = 'Passwords do not match.';
    elseif ($skillLevel < 1 || $skillLevel > 10)        $error = 'Skill level must be between 1 and 10.';
    elseif (!in_array($position, ['left','right','both'], true)) $error = 'Invalid preferred position.';
    elseif (!in_array($hand,     ['left','right'],        true)) $error = 'Invalid playing hand.';
    elseif (!$terms)                                    $error = 'You must agree to the Terms of Service.';

    if (!$error) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'This email is already registered. Please log in.';
            } else {
                $hashed = password_hash($passwordInput, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'INSERT INTO users
                        (first_name, last_name, email, phone, password_hash,
                         skill_level, preferred_position, playing_hand)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $firstName, $lastName, $email, $phone, $hashed,
                    $skillLevel, $position, $hand,
                ]);

                $_SESSION['user_id']    = (int)$pdo->lastInsertId();
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name']  = $lastName;
                $_SESSION['email']      = $email;

                header('Location: ../html/acceuil.html');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Sign-up DB error: ' . $e->getMessage());
            $error = 'A database error occurred. Please try again later.';
        }
    }
}

// Render the view — $error and $_POST are in scope inside the template.
include __DIR__ . '/../html/sign_up.html';
