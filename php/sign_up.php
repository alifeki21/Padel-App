<?php
require_once __DIR__ . '/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (currentUser()) {
    header('Location: ' . projectUrl('html/u_are_signed_in.html'));
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

                header('Location: ' . projectUrl('html/acceuil.html'));
                exit;
            }
        } catch (PDOException $e) {
            error_log('Sign-up DB error: ' . $e->getMessage());
            $error = 'A database error occurred. Please try again later.';
        }
    }
}

function url(string $p): string { return projectUrl($p); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up - Padel Badeli 7yeti</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?= url('images/logo.png') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('css/sign_up.css') ?>">
    <link rel="stylesheet" href="<?= url('css/header.css') ?>">
</head>
<body>
    <div id="root">
        <div class="App">
            <header class="site-header">
                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="container">
                        <a class="navbar-brand" href="<?= url('html/acceuil.html') ?>">
                            <img src="<?= url('images/logo1.png') ?>" height="45" alt="Casa del Padel">
                        </a>
                        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="<?= url('html/acceuil.html') ?>">Accueil</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= url('html/reservation.html') ?>">Réservation</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= url('html/tournois.html') ?>">Tournois</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= url('html/ContactUs.html') ?>">Contactez-nous</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= url('php/login.php') ?>">Connexion</a></li>
                                <li class="nav-item"><a class="nav-link active" href="<?= url('php/sign_up.php') ?>">S’inscrire</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <section class="on">
                <div class="signup-container">
                    <div class="signup-form-container">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form class="signup-form" method="POST">
                            <div class="form-header">
                                <div class="logo">
                                    <i class="fas fa-table-tennis"></i>
                                    <h1>Padel Badeli 7yeti</h1>
                                </div>
                                <h2>Create Your Account</h2>
                                <p>Join our community to book courts and connect with players</p>
                            </div>

                            <div class="form-row">
                                <div class="form-group" id="firstNameGroup">
                                    <label for="firstName">First Name</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Enter your first name" value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
                                    </div>
                                    <div class="error-message">First name is required</div>
                                </div>
                                <div class="form-group" id="lastNameGroup">
                                    <label for="lastName">Last Name</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Enter your last name" value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
                                    </div>
                                    <div class="error-message">Last name is required</div>
                                </div>
                            </div>

                            <div class="form-group" id="emailGroup">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="error-message">Please enter a valid email address</div>
                            </div>

                            <div class="form-group" id="phoneGroup">
                                <label for="phone">Phone Number</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                </div>
                                <div class="error-message">Please enter a valid phone number</div>
                            </div>

                            <div class="form-row">
                                <div class="form-group" id="passwordGroup">
                                    <label for="password">Password</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                                    </div>
                                    <div class="error-message">Password must be at least 8 characters</div>
                                </div>
                                <div class="form-group" id="confirmPasswordGroup">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
                                    </div>
                                    <div class="error-message">Passwords do not match</div>
                                </div>
                            </div>

                            <div class="form-group" id="levelGroup">
                                <label for="level">Skill Level (1-10)</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-chart-bar"></i>
                                    <input type="number" id="level" name="level" class="form-control" placeholder="Enter skill level (e.g., 5.5)" min="1" max="10" step="0.1" value="<?= htmlspecialchars($_POST['level'] ?? '') ?>" required>
                                </div>
                                <div class="error-message">Please enter a number between 1 and 10</div>
                                <div class="skill-level-hint">
                                    <small>1 = Beginner, 10 = Professional (decimals like 3.5, 7.2 are allowed)</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group" id="positionGroup">
                                    <label for="position">Preferred Position</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        <select id="position" name="position" class="form-control" required>
                                            <option value="">Select your position</option>
                                            <option value="left"  <?= (($_POST['position'] ?? '') === 'left')  ? 'selected' : '' ?>>Left Side</option>
                                            <option value="right" <?= (($_POST['position'] ?? '') === 'right') ? 'selected' : '' ?>>Right Side</option>
                                            <option value="both"  <?= (($_POST['position'] ?? '') === 'both')  ? 'selected' : '' ?>>Both Sides</option>
                                        </select>
                                    </div>
                                    <div class="error-message">Please select your position</div>
                                </div>
                                <div class="form-group" id="handGroup">
                                    <label for="hand">Playing Hand</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-hand-paper"></i>
                                        <select id="hand" name="hand" class="form-control" required>
                                            <option value="">Select your hand</option>
                                            <option value="right" <?= (($_POST['hand'] ?? '') === 'right') ? 'selected' : '' ?>>Right-handed</option>
                                            <option value="left"  <?= (($_POST['hand'] ?? '') === 'left')  ? 'selected' : '' ?>>Left-handed</option>
                                        </select>
                                    </div>
                                    <div class="error-message">Please select your playing hand</div>
                                </div>
                            </div>

                            <div class="options">
                                <div class="terms">
                                    <input type="checkbox" id="terms" name="terms" value="1" <?= isset($_POST['terms']) ? 'checked' : '' ?> required>
                                    <label for="terms">
                                        I agree to the
                                        <a href="<?= url('html/terms.html') ?>" target="_blank">Terms of Service</a>
                                        and
                                        <a href="<?= url('html/privacy.html') ?>" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            <div class="error-message" id="termsError">You must agree to the terms and conditions</div>

                            <button type="submit" class="submit-btn">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>

                            <div class="login-link">
                                Already have an account? <a href="<?= url('php/login.php') ?>">Log in here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('js/sign_up.js') ?>"></script>
</body>
</html>