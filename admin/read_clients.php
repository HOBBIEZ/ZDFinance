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
            List of Clients
            <a class="btn btn-primary" href="/POS_website/admin/create_client.php" role="button">New Client</a>
        </h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Creation Date</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>DoB</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Action</th>
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

                $sql = "SELECT * FROM Users";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[UserID]</td>
                        <td>$row[Username]</td>
                        <td>$row[Password]</td>
                        <td>$row[Creation_Date]</td>
                        <td>$row[First_Name]</td>
                        <td>$row[Last_Name]</td>
                        <td>$row[Date_of_Birth]</td>
                        <td>$row[Gender]</td>
                        <td>$row[Email]</td>
                        <td>$row[Phone_Number]</td>
                        <td>$row[Address]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/POS_website/admin/edit_client.php?UserID=$row[UserID]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/POS_website/admin/delete_client.php?UserID=$row[UserID]'>Delete</a>
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
