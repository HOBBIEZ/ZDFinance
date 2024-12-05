<?php
session_start();
include('db_connection.php');  // Include DB connection script

$username = $_SESSION['user'];

// Prepare SQL statement to fetch the user's ID based on their username
$query = "SELECT UserID FROM Users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$UserID = $result->fetch_assoc()['UserID'];

// Combine and sort transactions
$query = "
    SELECT 
        Timestamp AS Date, 
        'external' AS TransactionType,  
        Amount AS Amount, 
        IBAN AS Source,
        IBAN AS Destination, 
        NULL AS Description, 
        Type AS Type 
    FROM External_Transactions
    WHERE UserID = ?

    UNION ALL

    SELECT 
        Timestamp  AS Date,
        'internal' AS TransactionType,  
        Amount AS Amount, 
        From_IBAN AS Source, 
        To_IBAN AS Destination, 
        Description AS Description,
        'Sent' AS Type
    FROM Internal_Transactions
    WHERE From_IBAN IN (SELECT IBAN FROM Accounts WHERE UserID = ?)

    UNION ALL

    SELECT 
        Timestamp  AS Date,
        'internal' AS TransactionType,  
        Amount AS Amount, 
        From_IBAN AS Source, 
        To_IBAN AS Destination, 
        Description AS Description,
        'Received' AS Type
    FROM Internal_Transactions
    WHERE To_IBAN IN (SELECT IBAN FROM Accounts WHERE UserID = ?)

    ORDER BY Date;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $UserID, $UserID, $UserID);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $data = array_values($data);
    echo json_encode($data);
} else {
    echo json_encode(array("error" => "User not found"));
}

$stmt->close();
$conn->close();
?>
