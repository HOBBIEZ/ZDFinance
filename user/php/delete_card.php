<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = $_POST['cardNumber'];
    $iban = $_POST['iban'];

    // SQL query to delete the card from the database
    $stmt = $conn->prepare("DELETE FROM cards WHERE Card_Number = ? AND IBAN = ?");
    if ($stmt->execute([$cardNumber, $iban])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete card']);
    }
}
?>
