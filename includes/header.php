<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitQuest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ONLY THIS CSS FILE -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header>
    <h1>@FitQuest</h1>
    <nav>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a> |
            <a href="workouts.php">Workouts</a> |
            <a href="progress_new.php">Log Progress</a> |
            <a href="progress_list.php">Progress History</a> |
            <a href="analytics.php">Analytics</a> |
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> |
            <a href="login.php">Sign Up</a>
        <?php endif; ?>

        <!-- dark mode slider -->
        <div class="theme-toggle">
            <span>Dark/Light mode</span>
            <img id="themeToggle" src="img/toggle_off.png" alt="Toggle dark mode">
        </div>
    </nav>
</header>
<main>
