<?php
include('db_connection.php');
session_start();

error_log("User: " . $_SESSION['user']); 

$stmt = $conn->prepare("CALL log_user_logout(?)");
$stmt->bind_param('s', $_SESSION['user']);
$stmt->execute();
// Destroy the session
session_unset();
session_destroy();

// Redirect to the login page or return a confirmation
header('Location: ../pages/boot.html');
exit;
?>
