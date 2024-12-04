<?php
session_start();
// Include database connection
include('db_connection.php');

$username = $_SESSION['user'];

if ($_POST['iban'] === $_POST['receivers_iban']) {
    echo json_encode(['success' => false, 'error' => 'Error: Source and destination IBAN cannot be the same.']);
    return;
}


$stmt = $conn->prepare("SELECT * FROM Accounts WHERE IBAN = ?");
$stmt->bind_param("s", $_POST['receivers_iban']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    echo json_encode(['success' => false, 'error' => 'Error: Destination IBAN does not exist.']);
    return;
}

// Prepare SQL statement to fetch the user's ID based on their username
$query = "SELECT UserID FROM Users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$UserID = $result->fetch_assoc()['UserID'];

$query = "SELECT Balance FROM Accounts WHERE IBAN = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_POST['iban']);
$stmt->execute();
$result = $stmt->get_result();
$balance = $result->fetch_assoc()['Balance'];

error_log((float)$balance - (float)$_POST['amount']);

if ((float)$balance - (float)$_POST['amount'] < 0) {
    echo json_encode(['success' => false, 'error' => 'Your balance is insufficient.']);
    $stmt->close();
    return;
}

$stmt = $conn->prepare("INSERT INTO Internal_Transactions (UserID, From_IBAN, To_IBAN, Description, Amount) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('issss', $UserID, $_POST['iban'], $_POST['receivers_iban'], $_POST['transaction_description'], $_POST['amount']);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Transaction failed.']);
}

$stmt->close();
?>
