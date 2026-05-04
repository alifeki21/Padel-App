<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'padel_db';
$username = 'root';
$password = ''; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $passwordInput = $_POST['password'] ?? '';
    $confirm   = $_POST['confirmPassword'] ?? '';
    $skillLevel= floatval($_POST['level'] ?? 0);
    $position  = $_POST['position'] ?? '';
    $hand      = $_POST['hand'] ?? '';
    $terms     = isset($_POST['terms']);

    if (!$firstName) $error = "First name is required.";
    elseif (!$lastName) $error = "Last name is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Valid email is required.";
    elseif (!$phone) $error = "Phone number is required.";
    elseif (strlen($passwordInput) < 8) $error = "Password must be at least 8 characters.";
    elseif ($passwordInput !== $confirm) $error = "Passwords do not match.";
    elseif ($skillLevel < 1 || $skillLevel > 10) $error = "Skill level must be between 1 and 10.";
    elseif (!in_array($position, ['left','right','both'])) $error = "Invalid position.";
    elseif (!in_array($hand, ['left','right'])) $error = "Invalid hand.";
    elseif (!$terms) $error = "You must agree to the Terms of Service.";

    if (!$error) {
        try {
            $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            $pdo->exec("USE $dbname");

            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(180) UNIQUE NOT NULL,
                phone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NOT NULL,
                skill_level DECIMAL(3,1) NOT NULL,
                position ENUM('left','right','both') NOT NULL,
                hand ENUM('left','right') NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "This email is already registered. Please log in.";
            } else {
                $hashed = password_hash($passwordInput, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, skill_level, position, hand)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $phone, $hashed, $skillLevel, $position, $hand]);
                $success = "Account created successfully! <a href='login.html'>Log in here</a>";
                $_POST = [];
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up - Padel Badeli 7yeti</title>
    <link rel="stylesheet" href="css/sign_up.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <div id="root">
        <div class="App">
            <header class="site-header">
                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="container">
                        <a class="navbar-brand" href="acceuil.html">
                            <img src="images/logo1.png" height="45" alt="Casa del Padel">
                        </a>
                        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="acceuil.html">Accueil</a></li>
                                <li class="nav-item"><a class="nav-link" href="reservation.html">Réservation</a></li>
                                <li class="nav-item"><a class="nav-link" href="tournois.html">Tournois</a></li>
                                <li class="nav-item"><a class="nav-link" href="ContactUs.html">Contactez-nous</a></li>
                                <li class="nav-item"><a class="nav-link" href="login.html">Connexion</a></li>
                                <li class="nav-item"><a class="nav-link active" href="sign_up.php">S’inscrire</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <section class="on">
                <div class="signup-container">
                    <div class="signup-form-container">
                        <?php if ($error): ?>
                            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form class="signup-form" method="POST" action="" id="signupForm">
                            <div class="form-header">
                                <div class="logo">
                                    <i class="fas fa-table-tennis"></i>
                                    <h1>Padel Badeli 7yeti</h1>
                                </div>
                                <h2>Create Your Account</h2>
                                <p>Join our community to book courts and connect with players</p>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Enter your first name" value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Enter your last name" value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="password-eye-row">
                                        <div class="input-with-icon">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                                        </div>
                                        <button type="button" class="eye-toggle-btn" onclick="togglePassword('password', this)">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <div class="password-eye-row">
                                        <div class="input-with-icon">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
                                        </div>
                                        <button type="button" class="eye-toggle-btn" onclick="togglePassword('confirmPassword', this)">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="level">Skill Level (1-10)</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-chart-bar"></i>
                                    <input type="number" id="level" name="level" class="form-control" placeholder="Enter skill level (e.g., 5.5)" min="1" max="10" step="0.1" value="<?= htmlspecialchars($_POST['level'] ?? '') ?>" required>
                                </div>
                                <div class="skill-level-hint">
                                    <small>1 = Beginner, 10 = Professional (decimals like 3.5, 7.2 are allowed)</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="position">Preferred Position</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        <select id="position" name="position" class="form-control" required>
                                            <option value="">Select your position</option>
                                            <option value="left" <?= (($_POST['position'] ?? '') == 'left') ? 'selected' : '' ?>>Left Side</option>
                                            <option value="right" <?= (($_POST['position'] ?? '') == 'right') ? 'selected' : '' ?>>Right Side</option>
                                            <option value="both" <?= (($_POST['position'] ?? '') == 'both') ? 'selected' : '' ?>>Both Sides</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="hand">Playing Hand</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-hand-paper"></i>
                                        <select id="hand" name="hand" class="form-control" required>
                                            <option value="">Select your hand</option>
                                            <option value="right" <?= (($_POST['hand'] ?? '') == 'right') ? 'selected' : '' ?>>Right-handed</option>
                                            <option value="left" <?= (($_POST['hand'] ?? '') == 'left') ? 'selected' : '' ?>>Left-handed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="options">
                                <div class="terms">
                                    <input type="checkbox" id="terms" name="terms" value="1" <?= isset($_POST['terms']) ? 'checked' : '' ?> required>
                                    <label for="terms">
                                        I agree to the 
                                        <a href="terms.html" target="_blank">Terms of Service</a> 
                                        and 
                                        <a href="privacy.html" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="submit-btn">
                                <i class="fas fa-user-plus"></i>Create Account
                            </button>
                            <div class="login-link">
                                Already have an account? <a href="login.html">Log in here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
    <script src="js/sign_up.js"></script>
</body>
</html>