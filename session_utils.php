<?php
function start_secure_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}


function is_logged_in() {
    start_secure_session();
    return isset($_SESSION['user_id']);
}


function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}


function logout() {
    start_secure_session();
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}
?>