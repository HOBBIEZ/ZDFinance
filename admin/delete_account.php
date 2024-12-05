<?php

if ( isset($_GET["IBAN"]) ) {

    $IBAN = $_GET["IBAN"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "zdfinance";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "UPDATE Accounts SET Status='inactive' WHERE IBAN='$IBAN'";
    $connection->query($sql);
}

header("location: /ZDFinance/admin/read_accounts.php");
exit;

?>
