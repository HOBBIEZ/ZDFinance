<?php

if ( isset($_GET["IBAN"]) ) {

    // deleting according to unique field - retrieve the data
    $IBAN = $_GET["IBAN"];

    // connect with db
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pos_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // for the specific logic -> delete = set status to 'inactive'
    $sql = "UPDATE Accounts SET Status='inactive' WHERE IBAN='$IBAN'";
    $connection->query($sql);
}

header("location: /ZDFinance/admin/read_accounts.php");
exit;

?>
