<?php

if ( isset($_GET["UserID"]) ) {

    // deleting according to unique field - retrieve the data
    $UserID = $_GET["UserID"];

    // connect with db
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pos_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // for the specific logic -> delete = set status to 'deleted'
    $sql = "UPDATE Users SET Status='deleted' WHERE UserID='$UserID'";
    $connection->query($sql);
}

header("location: /ZDFinance/admin/read_clients.php");
exit;

?>
