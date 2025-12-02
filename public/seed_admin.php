<?php
// Temporary admin session helper for development/testing only
// Creates a session and marks the user as admin, then redirects to the admin posts page.
// Remove this file before deploying to production.

require_once __DIR__ . '/../app/Config/config.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$_SESSION['is_admin'] = 1;

// Redirect to admin posts route
header('Location: ' . URLROOT . '/admin/posts');
exit;
