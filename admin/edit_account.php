<?php

// connect with db
$servername = "localhost";
$username = "root";
$password = "";
$database = "zdfinance";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// initialize necessary variables
$account_name = "";
$status = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["IBAN"])) {
        header("location: /ZDFinance/admin/edit_account.php");
        exit;
    }

    $IBAN = $_GET["IBAN"];

    // Use prepared statements for security
    $stmt = $connection->prepare("SELECT * FROM Accounts WHERE IBAN = ?");
    $stmt->bind_param("s", $IBAN);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /ZDFinance/admin/read_accounts.php");
        exit;
    }

    // load the existence data of the fields
    $account_name = $row["Account_Name"];
    $status = $row["Status"];
    $stmt->close();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // apply new data from front end gui HTML to the values of the php variables
    $IBAN = isset($_POST["IBAN"]) ? $_POST["IBAN"] : '';
    $account_name = isset($_POST["Account_Name"]) ? $_POST["Account_Name"] : '';
    $status = isset($_POST["Status"]) ? $_POST["Status"] : '';

    do {
        if (empty($IBAN) || empty($account_name) || empty($status)) {
            $errorMessage = "All fields are required";
            break;
        }

        // Use prepared statements for security
        $stmt = $connection->prepare("UPDATE Accounts SET Account_Name = ?, Status = ? WHERE IBAN = ?");
        $stmt->bind_param("sss", $account_name, $status, $IBAN);

        if (!$stmt->execute()) {
            $errorMessage = "Invalid query: " . $stmt->error;
            break;
        }

        $successMessage = "Account updated successfully!";
        $stmt->close();

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
    <title>Edit Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Edit Account</h2>
        <br><br>

        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post">
            <input type="hidden" name="IBAN" value="<?php echo htmlspecialchars($IBAN); ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Account Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Account_Name" value="<?php echo htmlspecialchars($account_name); ?>" required>
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
            if (!empty($successMessage)) {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong>$successMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
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
