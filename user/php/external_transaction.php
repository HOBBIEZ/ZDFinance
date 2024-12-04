<?php
session_start();
// Include database connection
include('db_connection.php');

$username = $_SESSION['user'];

// Prepare SQL statement to fetch the user's ID based on their username
$query = "SELECT UserID FROM Users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$UserID = $result->fetch_assoc()['UserID'];

if ($_POST['type'] === 'withdraw') {
    $query = "SELECT Balance FROM Accounts WHERE IBAN = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_POST['iban']);
    $stmt->execute();
    $result = $stmt->get_result();
    $balance = $result->fetch_assoc()['Balance'];

    if ((float)$balance - (float)$_POST['amount'] < 0) {
        echo json_encode(['success' => false, 'error' => 'Your balance is insufficient.']);
        $stmt->close();
        return;
    }
}

$stmt = $conn->prepare("INSERT INTO External_Transactions (UserID, IBAN, Type, Amount) VALUES (?, ?, ?, ?)");
$stmt->bind_param('isss', $UserID, $_POST['iban'], $_POST['type'], $_POST['amount']);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to deposit funds.']);
}

$stmt->close();
?>
