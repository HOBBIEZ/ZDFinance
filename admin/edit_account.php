<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "pos_db";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$account_name = "";
$status = "";

$errorMessage   = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["IBAN"])) {
        header("location: /ZDFinance/admin/edit_account.php");
        exit;
    }

    $IBAN = $_GET["IBAN"];

    $sql = "SELECT * FROM Accounts WHERE IBAN=$IBAN";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /ZDFinance/admin/read_accounts.php");
        exit;
    }

    $account_name = $row["Account_Name"];
    $status = $row["Status"];
} else {
    $IBAN = isset($_POST["IBAN"]) ? $_POST["IBAN"] : '';
    $account_name = isset($_POST["Account_name"]) ? $_POST["Account_Name"] : '';
    $status = isset($_POST["Status"]) ? $_POST["Status"] : '';

    do {
        if (empty($account_name) || empty($status) || empty($IBAN)) {
            $errorMessage = "All fields are required";
            break;
        }

        $sql = "UPDATE Accounts " .
               "SET Account_Name='$account_name', Status='$status'" .
               "WHERE IBAN=$IBAN";

        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $successMessage = "Account updated successfully!";
        header("location: /ZDFinance/admin/read_accounts.php");
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
        <h2>Edit Account</h2>
        <br><br>

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
            <input type="hidden" name="IBAN" value="<?php echo $IBAN; ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Account Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Account_Name" value="<?php echo $account_name; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-6">
                    <select class="form-control" name="Status" required>
                        <option value="">Select Status</option>
                        <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>active</option>
                        <option value="inactive" <?php echo $status == 'inactive' ? 'selected' : ''; ?>>inactive</option>
                    </select>
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
                    <a class="btn btn-outline-primary" href="/ZDFinance/admin/read_accounts.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>    
</body>
</html>