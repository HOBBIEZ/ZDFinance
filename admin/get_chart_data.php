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

// Fetch Users Status Distribution
$users_status_query = "SELECT Status AS label, COUNT(*) AS count FROM Users GROUP BY Status";
$users_status_result = $conn->query($users_status_query);

$users_status_data = [];
while ($row = $users_status_result->fetch_assoc()) {
    $users_status_data[] = $row;
}

// Fetch Account Balances Distribution
$account_balances_query = "
    SELECT
        CASE
            WHEN Balance < 10000 THEN 'Below $10,000'
            WHEN Balance BETWEEN 10000 AND 50000 THEN '$10,000 - $50,000'
            ELSE 'Above $50,000'
        END AS label,
        COUNT(*) AS count
    FROM Accounts
    GROUP BY label
";
$account_balances_result = $conn->query($account_balances_query);

$account_balances_data = [];
while ($row = $account_balances_result->fetch_assoc()) {
    $account_balances_data[] = $row;
}

// Fetch Transaction Types
$transaction_types_query = "
    SELECT Type AS label, COUNT(*) AS count
    FROM External_Transactions
    GROUP BY Type
";
$transaction_types_result = $conn->query($transaction_types_query);

$transaction_types_data = [];
while ($row = $transaction_types_result->fetch_assoc()) {
    $transaction_types_data[] = $row;
}

// Encode data for JSON
$response = [
    "users_status_data" => $users_status_data,
    "account_balances_data" => $account_balances_data,
    "transaction_types_data" => $transaction_types_data,
];

header('Content-Type: application/json');
echo json_encode($response);
?>
