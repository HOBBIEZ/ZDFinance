<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "pos_db";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$Card_Number      =  "";
$CVV              =  "";
$PIN              =  "";
$Purchase_Limit   =  "";
$Status           =  "";
$Expiration_Date  =  "";

$errorMessage   = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["Card_Number"])) {
        header("location: /POS/admin/edit_card.php");
        exit;
    }

    $Card_Number = $_GET["Card_Number"];

    $sql = "SELECT * FROM Cards WHERE Card_Number='$Card_Number'";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /POS/admin/read_cards.php");
        exit;
    }

    $Card_Number = $row["Card_Number"];
    $CVV = $row["CVV"];
    $PIN = $row["PIN"];
    $Purchase_Limit = $row["Purchase_Limit"];
    $Status = $row["Status"];
    $Expiration_Date = $row["Expiration_Date"];

} else {
    $Card_Number = isset($_POST["Card_Number"]) ? $_POST["Card_Number"] : '';
    $CVV = isset($_POST["CVV"]) ? $_POST["CVV"] : '';
    $PIN = isset($_POST["PIN"]) ? $_POST["PIN"] : '';
    $Purchase_Limit = isset($_POST["Purchase_Limit"]) ? $_POST["Purchase_Limit"] : '';
    $Status = isset($_POST["Status"]) ? $_POST["Status"] : '';
    $Expiration_Date = isset($_POST["Expiration_Date"]) ? $_POST["Expiration_Date"] : '';

    do {
        if ( empty($Card_Number) || empty($CVV) || empty($PIN) ||
             empty($Purchase_Limit) || empty($Status) || empty($Expiration_Date)
        ) {
            $errorMessage = "All fields are required";
            break;
        }

        $sql = "UPDATE Cards " .
               "SET Card_Number='$Card_Number', CVV='$CVV', Pin='$PIN', Purchase_Limit='$Purchase_Limit', Status='$Status', Expiration_Date='$Expiration_Date'" .
               "WHERE Card_Number='$Card_Number'";

        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $successMessage = "Card updated successfully!";
        header("location: /POS/admin/read_cards.php");
        exit;
    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2>New Card</h2>

        <?php
        if ( !empty($errorMessage) ) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post">
            <input type="hidden" name="Card_Number" value="<?php echo $Card_Number; ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Card Number</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Card_Number" value="<?php echo $Card_Number; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">CVV</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="CVV" value="<?php echo $CVV; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">PIN</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="PIN" value="<?php echo $PIN; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Purchase Limit</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Purchase_Limit" value="<?php echo $Purchase_Limit; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Status" value="<?php echo $Status; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Expiration Date</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Expiration_Date" value="<?php echo $Expiration_Date; ?>">
                </div>
            </div>

            <?php
            if ( !empty($successMessage) ) {
                echo "
                <div class='row mb-3'>
                    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <strong>$successMessage</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>
                ";
            }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/POS/admin/read_cards.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>    
</body>
</html>