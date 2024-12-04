<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pos_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Query to get transaction type distribution
$query = "SELECT type, COUNT(*) as count FROM transactions GROUP BY type";
$result = $conn->query($query);

$labels = [];
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = ucfirst($row['type']); // Capitalize transaction types
        $data[] = $row['count'];
    }
    $result->free();
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'data' => $data]);
