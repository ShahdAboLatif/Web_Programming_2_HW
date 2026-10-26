<?php
session_start();

// Uunset all session variables
$_SESSION = array();

// destroy the session
session_destroy();

// redirect back to login page
header("Location: login.html");
exit();
?>