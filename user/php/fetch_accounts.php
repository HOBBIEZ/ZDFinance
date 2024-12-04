<?php
session_start();
include('db_connection.php');  // Include your DB connection script

$username = $_SESSION['user'];

// Prepare SQL statement to fetch the user's ID based on their username
$query = "SELECT UserID FROM Users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $UserID = $result->fetch_assoc()['UserID'];
    
    // Fetch accounts and associated cards for the user
    $query = "
        SELECT 
            Accounts.Account_Name, 
            Accounts.IBAN, 
            Accounts.Balance, 
            Cards.Card_Number, 
            Cards.CVV, 
            Cards.PIN, 
            Cards.Expiration_Date
        FROM 
            Accounts
        LEFT JOIN 
            Cards ON Accounts.IBAN = Cards.IBAN
        WHERE 
            Accounts.UserID = ? AND Accounts.Status = 'active'
        ORDER BY 
    Accounts.Account_Name ASC;
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    
    // Grouping cards under each account
    while ($row = $result->fetch_assoc()) {
        $IBAN = $row['IBAN'];
        
        // If the account is not yet in the $data array, add it
        if (!isset($data[$IBAN])) {
            $data[$IBAN] = [
                'Account_Name' => $row['Account_Name'],
                'IBAN' => $row['IBAN'],
                'Balance' => $row['Balance'],
                'Cards' => []
            ];
        }
        
        // If there's a card, add it to the 'Cards' array for this account
        if ($row['Card_Number']) {
            $data[$IBAN]['Cards'][] = [
                'Card_Number' => $row['Card_Number'],
                'CVV' => $row['CVV'],
                'PIN' => $row['PIN'],
                'Expiration_Date' => $row['Expiration_Date']
            ];
        }
    }

    // Re-index the $data array to reset the keys
    $data = array_values($data);

    echo json_encode($data);
} else {
    echo json_encode(array("error" => "User not found"));
}

$stmt->close();
$conn->close();
?>
