<?php
// includes/auth.php

// Start session once, here
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Require a real login: if not logged in, send to login page
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Get current user id
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
