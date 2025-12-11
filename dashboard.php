<?php
// dashboard.php
require_once 'includes/auth.php';
require_once 'config/db.php';

// Make sure session exists so we can check login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/header.php';
?>

<div class="page-card">
    <h2>Welcome to FitQuest</h2>
    <p>
        Track your workouts, monitor your progress, and stay accountable to your fitness goals.
    </p>

    <p style="margin: 20px 0;">
        <a href="login.php" class="btn-primary">
            Log In / Sign Up
        </a>
    </p>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <p>
            You’re already logged in. Go to your
            <a href="workouts.php">Workout Plans</a>
            or
            <a href="progress_list.php">Progress History</a>.
        </p>
    <?php else: ?>
        <p class="card-note">
            Already have an account? Click “Log In / Sign Up” above to access your dashboard.
        </p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
