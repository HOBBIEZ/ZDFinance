<?php
session_start();
include('db_connection.php');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if IBAN is provided
if (!isset($data['accountIBAN'])) {
    echo json_encode(['success' => false, 'error' => 'IBAN is missing']);
    exit;
}

$accountIBAN = $data['accountIBAN'];

// Prepare and execute the DELETE query
$sql = "UPDATE Accounts SET Status = 'inactive' WHERE IBAN = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $accountIBAN);

if ($stmt->execute()) {
    $stmt = $conn->prepare("DELETE FROM cards WHERE IBAN = ?");
    $stmt->bind_param('s', $accountIBAN);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete account']);
}

$stmt->close();
$conn->close();
?>
