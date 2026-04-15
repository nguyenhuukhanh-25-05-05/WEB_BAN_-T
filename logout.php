<?php
/**
 * NHK Mobile - Secure Session Termination
 * 
 * Description: Clears all authentication tokens and session data 
 * to securely log out users and administrators. Redirects to homepage.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.1
 * Date: 2026-04-08
 */
session_start();

// Flush all session variables
session_unset();

// Obliterate the session data
session_destroy();

// Return to global entry point
header("Location: index.php");
exit;
?>
