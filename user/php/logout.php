<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to the login page or return a confirmation
header('Location: ../pages/boot.html');
exit;
?>
