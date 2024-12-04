<?php

if ( isset($_GET["UserID"]) ) {

    $UserID = $_GET["UserID"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pos_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "UPDATE Users SET Status='deleted' WHERE UserID='$UserID'";
    $connection->query($sql);
}

header("location: /POS/admin/read_clients.php");
exit;

?>
