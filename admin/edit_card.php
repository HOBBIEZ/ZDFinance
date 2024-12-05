<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "pos_db";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$PIN = "";
$Card_Number = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["Card_Number"])) {
        header("location: /ZDFinance/admin/read_cards.php");
        exit;
    }

    $Card_Number = $_GET["Card_Number"];

    // Use a prepared statement for security
    $stmt = $connection->prepare("SELECT * FROM Cards WHERE Card_Number = ?");
    $stmt->bind_param("s", $Card_Number);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /ZDFinance/admin/read_cards.php");
        exit;
    }

    $Card_Number = $row["Card_Number"];
    $PIN = $row["PIN"];
    $stmt->close();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Card_Number = isset($_POST["Original_Card_Number"]) ? $_POST["Original_Card_Number"] : '';
    $PIN = isset($_POST["PIN"]) ? $_POST["PIN"] : '';

    do {
        if (empty($Card_Number) || empty($PIN)) {
            $errorMessage = "All fields are required.";
            break;
        }

        // Use a prepared statement for updating
        $stmt = $connection->prepare("UPDATE Cards SET PIN = ? WHERE Card_Number = ?");
        $stmt->bind_param("ss", $PIN, $Card_Number);

        if (!$stmt->execute()) {
            $errorMessage = "Invalid query: " . $stmt->error;
            break;
        }

        $successMessage = "Card updated successfully!";
        $stmt->close();

        header("location: /ZDFinance/admin/read_cards.php");
        exit;
    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Edit Card</h2>
        <br><br>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><?= htmlspecialchars($errorMessage); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="Original_Card_Number" value="<?= htmlspecialchars($Card_Number); ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">PIN</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control" name="PIN" value="<?= htmlspecialchars($PIN); ?>" maxlength="4" required>
                </div>
            </div>

            <?php if (!empty($successMessage)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><?= htmlspecialchars($successMessage); ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/ZDFinance/admin/read_cards.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
