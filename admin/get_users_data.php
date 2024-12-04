<?php

include('db_connection.php');

// Query to get user status distribution
$query = "SELECT status, COUNT(*) as count FROM users GROUP BY status";
$result = $conn->query($query);

$labels = [];
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = ucfirst($row['status']); // Capitalize 'active' and 'deleted'
        $data[] = $row['count'];
    }
    $result->free();
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'data' => $data]);
