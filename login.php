<?php
// login.php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors_login = [];
$errors_register = [];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'login';

    if ($mode === 'login') {
        $email    = trim($_POST['login_email'] ?? '');
        $password = $_POST['login_password'] ?? '';

        if ($email === '' || $password === '') {
            $errors_login[] = 'Please enter both email and password.';
        } else {
            $stmt = $mysqli->prepare("
                SELECT user_id, password_hash
                FROM users
                WHERE email = ?
            ");
            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->bind_result($uid, $hash);

                if ($stmt->fetch() && password_verify($password, $hash)) {
                    $_SESSION['user_id'] = $uid;
                    $stmt->close();
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $errors_login[] = 'Invalid email or password.';
                }

                $stmt->close();
            } else {
                $errors_login[] = 'Database error. Please try again later.';
            }
        }
    }

    elseif ($mode === 'register') {
        $full_name = trim($_POST['reg_name'] ?? '');
        $email     = trim($_POST['reg_email'] ?? '');
        $password  = $_POST['reg_password'] ?? '';
        $password2 = $_POST['reg_password2'] ?? '';

        if ($full_name === '' || $email === '' || $password === '' || $password2 === '') {
            $errors_register[] = 'All fields are required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors_register[] = 'Please enter a valid email address.';
        }
        if ($password !== $password2) {
            $errors_register[] = 'Passwords do not match.';
        }
        if (strlen($password) < 6) {
            $errors_register[] = 'Password must be at least 6 characters.';
        }

        if (empty($errors_register)) {
            $check = $mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
            if ($check) {
                $check->bind_param('s', $email);
                $check->execute();
                $check->store_result();
                if ($check->num_rows > 0) {
                    $errors_register[] = 'An account with that email already exists.';
                }
                $check->close();
            }

            if (empty($errors_register)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $ins = $mysqli->prepare("
                    INSERT INTO users (full_name, email, password_hash)
                    VALUES (?, ?, ?)
                ");
                if ($ins) {
                    $ins->bind_param('sss', $full_name, $email, $hash);
                    $ins->execute();
                    $new_id = $ins->insert_id;
                    $ins->close();

                    $_SESSION['user_id'] = $new_id;
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $errors_register[] = 'Failed to create account. Please try again.';
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="styles.css">

<div class="login-page">

    <!-- Left: simple welcome text -->
    <div class="login-left">
        <h2>@ FitQuest</h2>
        <p>Log in or create an account to start tracking your fitness journey.</p>
        <p class="login-subtext">
            Once signed in, you can build workout plans, log your progress, and see your history.
        </p>
    </div>

    <!-- Right: forms (always visible now) -->
    <div class="login-right">
        <h2>Account Access</h2>

        <div class="forms">

            <!-- SIGN IN -->
            <div class="form-box">
                <form method="post">
                    <h3>Sign In</h3>

                    <?php if (!empty($errors_login)): ?>
                        <div class="flash-errors">
                            <ul>
                                <?php foreach ($errors_login as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <input type="hidden" name="mode" value="login">

                    <input type="email"
                           name="login_email"
                           placeholder="Email">

                    <input type="password"
                           name="login_password"
                           placeholder="Password">

                    <button type="submit" class="btn-primary">Sign In</button>
                </form>
            </div>

            <!-- SIGN UP -->
            <div class="form-box">
                <form method="post">
                    <h3>Sign Up</h3>

                    <?php if (!empty($errors_register)): ?>
                        <div class="flash-errors">
                            <ul>
                                <?php foreach ($errors_register as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <input type="hidden" name="mode" value="register">

                    <input type="text"
                           name="reg_name"
                           placeholder="Full Name">

                    <input type="email"
                           name="reg_email"
                           placeholder="Email">

                    <input type="password"
                           name="reg_password"
                           placeholder="Password">

                    <input type="password"
                           name="reg_password2"
                           placeholder="Confirm Password">

                    <button type="submit" class="btn-primary">Create Account</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
