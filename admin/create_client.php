<?php

$servername = "localhost";
$username = "root";
$database = "pos_db";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$username = "";
$first_name = "";
$last_name = "";
$dob = "";
$gender = "";
$email = "";
$phone_num = "";
$address = "";
$status = "";

$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $username    =  $_POST["Username"];
    $password    =  $_POST["Password"];
    $first_name  =  $_POST["First_Name"];
    $last_name   =  $_POST["Last_Name"];
    $dob         =  $_POST["Date_of_Birth"];
    $gender      =  $_POST["Gender"];
    $email       =  $_POST["Email"];
    $phone_num   =  $_POST["Phone_Number"];
    $address     =  $_POST["Address"];
    $status      =  $_POST["Status"];

    do {
        if ( empty($username)  || empty($first_name) || empty($status)   ||
             empty($last_name) || empty($dob)        || empty($gender)   ||
             empty($email)     || empty($phone_num)  || empty($address) 
             
        ) {
            $errorMessage = "all fields are required";
            break;
        }

        $sql = "INSERT INTO Users (Username, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address, Status)
                VALUES ('$username', '$first_name', '$last_name', '$dob', '$gender', '$email', '$phone_num', '$address', '$status')";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
        }

        $username = "";
        $first_name = "";
        $last_name = "";
        $dob = "";
        $gender = "";
        $email = "";
        $phone_num = "";
        $address = "";
        $status = "";

        $successMessage = "Client added succesfully!";

        header("location: /ZDFinance/admin/read_clients.php");
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
        <h2>New Client</h2>
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
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Username" value="<?php echo $username; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">First Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="First_Name" value="<?php echo $first_name; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Last Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="Last_Name" value="<?php echo $last_name; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Date of Birth</label>
                <div class="col-sm-6">
                    <input type="date" class="form-control" name="Date_of_Birth" value="<?php echo $dob; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Gender</label>
                <div class="col-sm-6">
                    <select class="form-control" name="Gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo $gender == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $gender == 'female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="Email" value="<?php echo $email; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Phone Number</label>
                <div class="col-sm-6">
                    <input type="tel" class="form-control" name="Phone_Number" value="<?php echo $phone_num; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="Address" required><?php echo $address; ?></textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-6">
                    <select class="form-control" name="Status" required>
                        <option value="">Select Status</option>
                        <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>active</option>
                        <option value="deleted" <?php echo $status == 'deleted' ? 'selected' : ''; ?>>deleted</option>
                    </select>
                </div>
            </div>

            <?php
            if (!empty($successMessage)) {
                echo "
                <div class='row mb-3'>
                    <div class='alert alert-success alert-dismissible fade show' role='alert'>
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
                    <a class="btn btn-outline-primary" href="/ZDFinance/admin/read_clients.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
