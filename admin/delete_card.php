<?php

if ( isset($_GET["Card_Number"]) ) {

    // deleting according to unique field - retrieve the data
    $Card_Number = $_GET["Card_Number"];

    // connect with db
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pos_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // delete card according to unique card number
    $sql = "DELETE FROM Cards WHERE Card_Number='$Card_Number'";
    $connection->query($sql);
}

header("location: /ZDFinance/admin/read_cards.php");
exit;

?>
