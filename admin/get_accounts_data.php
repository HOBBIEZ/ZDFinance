<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zdfinance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Query to categorize account balances
$query = "
    SELECT 
        CASE 
            WHEN balance < 10000 THEN 'Below $10,000'
            WHEN balance BETWEEN 10000 AND 50000 THEN '$10,000 - $50,000'
            ELSE 'Above $50,000'
        END AS category,
        COUNT(*) as count
    FROM accounts
    GROUP BY category
    ORDER BY category
";
$result = $conn->query($query);

$labels = [];
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['category'];
        $data[] = $row['count'];
    }
    $result->free();
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'data' => $data]);
