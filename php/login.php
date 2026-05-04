<?php
require_once __DIR__ . '/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (currentUser()) {
    header('Location: ' . projectUrl('html/u_are_signed_in.html'));
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

                header('Location: ' . projectUrl('html/u_are_signed_in.html'));
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
function url(string $p): string { return projectUrl($p); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Log in - Padel Badeli 7yeti</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?= url('images/logo.png') ?>" />    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('css/login.css') ?>">
    <link rel="stylesheet" href="<?= url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= url('css/footer.css') ?>">
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
                                <li class="nav-item"><a class="nav-link"        href="<?= url('html/acceuil.html') ?>">Accueil</a></li>
                                <li class="nav-item"><a class="nav-link"        href="<?= url('html/reservation.html') ?>">Réservation</a></li>
                                <li class="nav-item"><a class="nav-link"        href="<?= url('html/tournois.html') ?>">Tournois</a></li>
                                <li class="nav-item"><a class="nav-link"        href="<?= url('html/ContactUs.html') ?>">Contactez-nous</a></li>
                                <li class="nav-item"><a class="nav-link active" href="<?= url('php/login.php') ?>">Connexion</a></li>
                                <li class="nav-item"><a class="nav-link"        href="<?= url('php/sign_up.php') ?>">S’inscrire</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <section class="on">
                <div class="login-container">
                    <div class="login-left">
                        <div class="logo">
                            <i class="fas fa-table-tennis"></i>
                            <h1>Padel Badeli 7yeti</h1>
                        </div>
                        <div class="welcome-text">
                            <h2>Welcome Back</h2>
                            <p>Log in to book courts, manage memberships, and enjoy the game you love.</p>
                        </div>
                        <div class="features">
                            <div class="feature">
                                <i class="fas fa-calendar-check"></i>
                                <span>Easy court booking &amp; reservation system</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-user-friends"></i>
                                <span>Find playing partners &amp; organize matches</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Reliable and tested levels</span>
                            </div>
                        </div>
                    </div>

                    <div class="login-right">
                        <form class="login-form" id="loginForm" method="POST" action="">
                            <h3>Log In</h3>

                            <?php if ($error): ?>
                                <div class="alert alert-error" style="background:#ffe5e5;color:#b00020;padding:10px 12px;border-radius:8px;margin-bottom:14px;border:1px solid #f5b5b5;">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group" id="emailGroup">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your Email address" value="<?= htmlspecialchars($emailVal) ?>" required>
                                </div>
                                <div class="error-message">Please enter a valid email address</div>
                            </div>

                            <div class="form-group" id="passwordGroup">
                                <label for="password">Password</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                                <div class="error-message">Password is required</div>
                            </div>

                            <div class="options">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember" name="remember" value="1">
                                    <label for="remember">Remember me</label>
                                </div>
                                <a href="<?= url('html/forgot_pasword.html') ?>" class="forgot-password">Forgot password?</a>
                            </div>

                            <button type="submit" class="submit-btn">
                                <i class="fas fa-sign-in-alt"></i> Log in
                            </button>

                            <div class="signup-link">
                                Don't have an account? <a href="<?= url('php/sign_up.php') ?>">Sign up now</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <footer class="site-footer bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-1">
                        <h4>À propos de nous</h4>
                        <br>
                        <p class="text-center-custom">
                            Padel, le point de rencontre des passionnés.
                        </p>
                    </div>
                    <div class="col-lg-2">
                        <h4>Coordonnées</h4>
                        <ul>
                            <li>📍 Insat, Centre Urbain Nord</li>
                            <li>📞 28 219 290</li>
                            <li>✉️ Contact@Padel.tn</li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h4>Liens utiles</h4>
                        <br>
                        <ul>
                            <li><a href="<?= url('html/acceuil.html') ?>">Accueil</a></li>
                            <li><a href="<?= url('html/reservation.html') ?>">Réservation</a></li>
                            <li><a href="<?= url('html/tournois.html') ?>">Tournois</a></li>
                            <li><a href="<?= url('html/ContactUs.html') ?>">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-4">
                    © 2026 – Conçu par
                    <p style="color: white;">Padel bedili 7yeti</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('js/login.js') ?>"></script>
</body>
</html>