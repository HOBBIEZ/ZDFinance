<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Webpage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2>
            List of Accounts
            <a class="btn btn-primary" href="/POS_website/admin/create_account.php" role="button">New Account</a>
        </h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>IBAN</th>
                    <th>Transfer Limit</th>
                    <th>Balance</th>
                    <th>Creation Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "pos_db";
                
                $connection = new mysqli($servername, $username, $password, $database);
                
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                $sql = "SELECT * FROM Accounts";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[UserID]</td>
                        <td>$row[IBAN]</td>
                        <td>$row[Transfer_Limit]</td>
                        <td>$row[Balance]</td>
                        <td>$row[Creation_Date]</td>
                        <td>$row[Status]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/POS_website/admin/edit_account.php?IBAN=$row[IBAN]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/POS_website/admin/delete_account.php?IBAN=$row[IBAN]'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
