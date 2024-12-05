<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iban = $_POST['iban'];
    $pin = $_POST['pin'];
    $username = $_SESSION['user'];  // Assuming session is used to track user
    // Prepare SQL statement to fetch the UserID
    $query = "SELECT UserID FROM Users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $UserID = $result->fetch_assoc()['UserID'];

    // Ensure IBAN belongs to the current user
    $sql = "SELECT * FROM Accounts WHERE IBAN = ? AND UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $iban, $UserID);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        // Proceed to insert card
        $sql = "INSERT INTO Cards (PIN, UserID, IBAN) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $pin, $UserID, $iban);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Account does not belong to the current user or does not exist.']);
    }
}
?>
