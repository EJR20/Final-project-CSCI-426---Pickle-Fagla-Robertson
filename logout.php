<?php
// logout.php
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = [];

// If you want to also kill the session cookie (extra safe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finally destroy the session
session_destroy();

include 'includes/header.php';
?>

<div class="page-card">
    <h2>You’ve Been Logged Out</h2>
    <p style="text-align:center; margin-bottom: 10px;">
        Thank you for staying consistent with your fitness journey!<br>
        You can log back in anytime to continue your progress.
    </p>

    <div class="form-container" style="margin-top: 15px; align-items: center;">
        <button type="button" onclick="window.location.href='login.php';">
            Return to Login
        </button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
