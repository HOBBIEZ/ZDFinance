<?php
// Database connection details
$servername = "localhost";
$username = "root"; // default username in XAMPP
$password = ""; // default empty password in XAMPP
$dbname = "pos_db"; // the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
