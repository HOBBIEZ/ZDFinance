<?php

if ( isset($_GET["IBAN"]) ) {

    $IBAN = $_GET["IBAN"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pos_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "DELETE FROM Accounts WHERE IBAN=$IBAN";
    $connection->query($sql);
}

header("location: /POS/admin/read_accounts.php");
exit;

?>
