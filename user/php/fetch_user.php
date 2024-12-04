<?php
session_start();
include('db_connection.php');  // Include your DB connection script

$username = $_SESSION['user'];

// Prepare SQL statement to fetch the user's data
$query = "SELECT Username, First_Name, Last_Name, Email, Phone_Number, Address FROM Users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
    echo json_encode($user);  // Return the data as JSON
} else {
    echo json_encode(array("error" => "User not found"));
}

$stmt->close();
$conn->close();
?>
